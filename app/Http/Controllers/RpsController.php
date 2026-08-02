<?php

namespace App\Http\Controllers;

use App\Models\Rps;
use App\Models\RpsSession;
use App\Models\RpsActivity;
use App\Models\Subject;
use App\Models\Clo;
use App\Models\Univ;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\RpsAssessment;
use App\Models\AssessmentType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use DB;

class RpsController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount(['rps' => function($query) {
            $query->whereHas('subject');
        }])->with('fakultas')->get();
        return view('admin.rps.index', compact('prodis'));
    }

    public function prodiRps(Request $request, Prodi $prodi)
    {
        $query = Rps::whereHas('subject', function($q) use ($prodi) {
            $q->where('id_prodi', $prodi->id);
        });

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_rps', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($sq) use ($search) {
                      $sq->where('nama_subject', 'like', "%{$search}%")
                        ->orWhere('kode_subject', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kurikulum', function($kq) use ($search) {
                      $kq->where('nm_kurikulum', 'like', "%{$search}%");
                  });
            });
        }

        $rps = $query->with(['subject', 'kurikulum'])->latest()->get();
        
        $subjects = Subject::where('id_prodi', $prodi->id)
            ->whereDoesntHave('rps')
            ->get();
        $kurikulums = Kurikulum::where('id_prodi', $prodi->id)
            ->orderBy('tahun_akademik', 'desc')
            ->get();
        $allProdis = Prodi::with(['subjects.rps', 'kurikulums'])->get();
        
        return view('admin.rps.prodi_rps', compact('rps', 'subjects', 'kurikulums', 'prodi', 'allProdis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'nomor_rps' => 'required|string|max:255',
            'tanggal_penyusunan' => 'required|date',
            'referensi' => 'nullable|string',
            'media_pembelajaran' => 'required|string',
            'pengembang_rps' => 'required|string',
            'dosen_pengampu' => 'required|string',
            'status' => 'nullable|in:Draft,Aktif,Arsip',
        ]);

        $data = $request->all();
        
        $latestRps = Rps::where('subject_id', $request->subject_id)->orderBy('versi', 'desc')->first();
        if ($latestRps) {
            $data['versi'] = $latestRps->versi + 1;
        } else {
            $data['versi'] = 1;
        }
        
        Rps::create($data);

        $subject = Subject::find($request->subject_id);
        if ($subject && $subject->id_prodi) {
            return redirect()->route('admin.rps.prodi', $subject->id_prodi)->with('success', 'RPS created successfully.');
        }

        return redirect()->route('admin.rps.index')->with('success', 'RPS created successfully.');
    }

    public function edit(Rps $rp)
    {
        $subjects = Subject::all();
        $kurikulums = Kurikulum::orderBy('tahun_akademik', 'desc')->get();
        return response()->json([
            'rps' => $rp,
            'subjects' => $subjects,
            'kurikulums' => $kurikulums
        ]);
    }

    public function update(Request $request, Rps $rp)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'nomor_rps' => 'required|string|max:255',
            'tanggal_penyusunan' => 'required|date',
            'referensi' => 'nullable|string',
            'media_pembelajaran' => 'required|string',
            'pengembang_rps' => 'required|string',
            'dosen_pengampu' => 'required|string',
            'status' => 'required|in:Draft,Aktif,Arsip',
        ]);

        $rp->update($request->all());

        if ($rp->subject && $rp->subject->id_prodi) {
            return redirect()->route('admin.rps.prodi', $rp->subject->id_prodi)->with('success', 'RPS updated successfully.');
        }

        return redirect()->route('admin.rps.index')->with('success', 'RPS updated successfully.');
    }

    public function destroy(Rps $rp)
    {
        $prodiId = optional($rp->subject)->id_prodi;
        
        if ($rp->isSyncedWithLms()) {
            return redirect()->back()->withErrors(['error' => 'RPS tidak dapat dihapus karena sudah disinkronisasikan dan digunakan di kelas LMS.']);
        }

        $hasActivities = \App\Models\RpsActivity::whereIn('rps_session_id', $rp->sessions->pluck('id'))->exists();
        $hasAssessments = \App\Models\RpsAssessment::whereIn('rps_session_id', $rp->sessions->pluck('id'))->exists();
        $hasFilledSessions = $rp->sessions()->where(function($q) {
            $q->whereNotNull('sub_clo')->where('sub_clo', '!=', '')
              ->orWhereNotNull('learning_materials')->where('learning_materials', '!=', '')
              ->orWhereNotNull('assessment_indicators')->where('assessment_indicators', '!=', '');
        })->exists();

        if ($hasActivities || $hasAssessments || $hasFilledSessions) {
            return redirect()->back()->withErrors(['error' => 'RPS tidak dapat dihapus karena sudah memiliki konten (Sesi Pembelajaran, Aktivitas, atau Assessment).']);
        }

        $rp->delete();
        
        if ($prodiId) {
            return redirect()->route('admin.rps.prodi', $prodiId)->with('success', 'RPS deleted successfully.');
        }
        return redirect()->route('admin.rps.index')->with('success', 'RPS deleted successfully.');
    }

    private function duplicateRps(Rps $oldRp, array $overrides)
    {
        DB::beginTransaction();
        try {
            $newRp = $oldRp->replicate();
            foreach ($overrides as $key => $val) {
                $newRp->$key = $val;
            }
            $newRp->save();

            $isSameSubject = ($oldRp->subject_id == $newRp->subject_id);

            // Duplicate Sessions, Activities, Assessments, Resources, and CLOs
            $oldRp->load(['sessions.activities', 'sessions.clos', 'sessions.assessments', 'sessions.resources']);
            foreach ($oldRp->sessions as $oldSession) {
                $newSession = $oldSession->replicate();
                $newSession->rps_id = $newRp->id;
                $newSession->save();

                // Clone activities
                foreach ($oldSession->activities as $oldActivity) {
                    $newActivity = $oldActivity->replicate();
                    $newActivity->rps_session_id = $newSession->id;
                    $newActivity->save();
                }

                // Clone assessments (Reset clo_id to null if cloning to a different subject/prodi)
                foreach ($oldSession->assessments as $oldAssessment) {
                    $newAssessment = $oldAssessment->replicate();
                    $newAssessment->rps_session_id = $newSession->id;
                    if (!$isSameSubject) {
                        $newAssessment->clo_id = null;
                    }
                    $newAssessment->save();
                }

                // Clone resources
                foreach ($oldSession->resources as $oldResource) {
                    $newResource = $oldResource->replicate();
                    $newResource->rps_session_id = $newSession->id;
                    $newResource->save();
                }

                // Clone CLO associations (Leave empty if cloning to a different subject/prodi for remapping)
                if ($isSameSubject) {
                    $newSession->clos()->sync($oldSession->clos->pluck('id'));
                } else {
                    $newSession->clos()->sync([]);
                }
            }

            DB::commit();
            return $newRp;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createNewVersion(Rps $rp)
    {
        // Ubah status yang lama jadi Arsip
        $rp->update(['status' => 'Arsip']);

        // Copy RPS jadi versi baru
        $this->duplicateRps($rp, [
            'versi' => $rp->versi + 1,
            'status' => 'Draft' // Atau 'Aktif'
        ]);

        return redirect()->route('admin.rps.index')->with('success', 'RPS versi baru berhasil dibuat. Versi lama telah diarsipkan.');
    }

    public function copyToKurikulum(Request $request, Rps $rp)
    {
        $request->validate([
            'kurikulum_id' => 'required|exists:kurikulums,id',
        ]);

        // Copy RPS ke kurikulum baru, versi reset ke 1
        $this->duplicateRps($rp, [
            'kurikulum_id' => $request->kurikulum_id,
            'versi' => 1,
            'status' => 'Draft'
        ]);

        return redirect()->route('admin.rps.index')->with('success', 'RPS berhasil dicopy ke Kurikulum baru.');
    }

    public function cloneToProdi(Request $request, Rps $rp)
    {
        $request->validate([
            'target_prodi_id' => 'required|exists:prodis,id',
            'target_subject_id' => 'required|exists:subjects,id',
            'target_kurikulum_id' => 'required|exists:kurikulums,id',
        ]);

        $targetSubject = Subject::with('prodi')->findOrFail($request->target_subject_id);

        if (Rps::where('subject_id', $targetSubject->id)->exists()) {
            return redirect()->back()->withErrors(['error' => 'Mata kuliah ' . $targetSubject->nama_subject . ' sudah memiliki RPS. Silakan pilih mata kuliah lain.'])->withInput();
        }

        $newRp = $this->duplicateRps($rp, [
            'subject_id' => $targetSubject->id,
            'kurikulum_id' => $request->target_kurikulum_id,
            'versi' => 1,
            'status' => 'Draft'
        ]);

        return redirect()->route('admin.rps.prodi', $request->target_prodi_id)->with('success', 'RPS berhasil di-clone ke Prodi ' . optional($targetSubject->prodi)->nama_prodi);
    }

    public function manageSessions(Rps $rp)
    {
        $rp->load('sessions.activities', 'sessions.clos', 'sessions.assessments', 'sessions.resources', 'subject.clos');
        $clos = $rp->subject->clos;

        // Calculate current total weight
        $totalWeight = $rp->sessions->flatMap->assessments->sum('weight');
        
        // Ensure 14 sessions exist
        if ($rp->sessions->count() < 14) {
            for ($i = 1; $i <= 14; $i++) {
                RpsSession::firstOrCreate([
                    'rps_id' => $rp->id,
                    'session_number' => $i,
                ], [
                    'topic_name' => 'Session ' . $i,
                    'sub_clo' => '',
                    'learning_materials' => '',
                    'assessment_indicators' => '',
                    'evaluation_criteria' => '',
                ]);
            }
            $rp->load('sessions.activities', 'sessions.clos', 'sessions.assessments');
        }

        return view('admin.rps.manage_sessions', compact('rp', 'clos', 'totalWeight'));
    }

    public function updateSession(Request $request, RpsSession $session)
    {
        $request->validate([
            'topic_name' => 'required|string',
            'sub_clo' => 'nullable|string',
            'learning_materials' => 'nullable|string',
            'assessment_indicators' => 'nullable|string',
            'evaluation_criteria' => 'nullable|string',
            'clos' => 'nullable|array',
            'activities' => 'nullable|array',
            'activities.*.type' => 'required_with:activities|string',
            'activities.*.duration' => 'required_with:activities|integer|min:1',
            'activities.*.content' => 'required_with:activities|string',
            'assessments' => 'nullable|array',
            'assessments.*.clo_id' => 'nullable|exists:clos,id',
            'assessments.*.assessment_type' => 'required_with:assessments|string',
            'assessments.*.assignment_activities' => 'nullable|string',
            'assessments.*.assessment_scope' => 'nullable|string',
            'assessments.*.how_worked' => 'nullable|string',
            'assessments.*.time_worked' => 'nullable|integer',
            'assessments.*.assessment_output' => 'nullable|string',
            'assessments.*.weight' => 'required_with:assessments|integer|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['clos', 'activities', 'assessments']);
            if (array_key_exists('sub_clo', $data) && is_null($data['sub_clo'])) {
                $data['sub_clo'] = '';
            }
            if (array_key_exists('learning_materials', $data) && is_null($data['learning_materials'])) {
                $data['learning_materials'] = '';
            }
            if (array_key_exists('assessment_indicators', $data) && is_null($data['assessment_indicators'])) {
                $data['assessment_indicators'] = '';
            }
            if (array_key_exists('evaluation_criteria', $data) && is_null($data['evaluation_criteria'])) {
                $data['evaluation_criteria'] = '';
            }
            
            $session->update($data);
            $session->clos()->sync($request->clos ?? []);

            // Handle Assessments
            $session->assessments()->delete();
            if ($request->has('assessments') && is_array($request->assessments)) {
                foreach ($request->assessments as $assess) {
                    $session->assessments()->create([
                        'clo_id' => !empty($assess['clo_id']) ? $assess['clo_id'] : null,
                        'assessment_type' => $assess['assessment_type'],
                        'assignment_activities' => $assess['assignment_activities'] ?? null,
                        'assessment_scope' => $assess['assessment_scope'] ?? null,
                        'how_worked' => $assess['how_worked'] ?? null,
                        'time_worked' => $assess['time_worked'] ?? null,
                        'assessment_output' => $assess['assessment_output'] ?? null,
                        'weight' => $assess['weight'],
                    ]);
                }
            }

            // Double check total weight for the whole RPS
            $totalWeight = RpsAssessment::whereIn('rps_session_id', $session->rps->sessions->pluck('id'))->sum('weight');
            if ($totalWeight > 100) {
                DB::rollBack();
                return redirect()->back()->withErrors(['error' => 'Total weight exceeds 100% (Current: ' . $totalWeight . '%)'])->withInput();
            }

            // Sync activities
            $session->activities()->delete();
            if ($request->has('activities') && is_array($request->activities)) {
                foreach ($request->activities as $activity) {
                    if (!empty($activity['content'])) {
                        $session->activities()->create([
                            'type' => $activity['type'],
                            'duration' => $activity['duration'] ?? 0,
                            'content' => $activity['content']
                        ]);
                    }
                }
            }

            // Handle Existing Resources (update or delete)
            if ($request->has('existing_resources') && is_array($request->existing_resources)) {
                foreach ($request->existing_resources as $resData) {
                    $resource = \App\Models\SessionResource::find($resData['id']);
                    if ($resource) {
                        if (isset($resData['delete']) && $resData['delete'] == 1) {
                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($resource->file_path)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($resource->file_path);
                            }
                            $resource->delete();
                        } else {
                            $resource->nm_resource = $resData['nm_resource'];
                            $resource->type = $resData['type'];
                            
                            if (isset($resData['file']) && $resData['file'] instanceof \Illuminate\Http\UploadedFile) {
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($resource->file_path)) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($resource->file_path);
                                }
                                $path = $resData['file']->store('session_resources', 'public');
                                $resource->file_path = $path;
                            }
                            $resource->save();
                        }
                    }
                }
            }

            // Handle New Resources
            if ($request->has('new_resources') && is_array($request->new_resources)) {
                foreach ($request->new_resources as $resData) {
                    if (isset($resData['file']) && $resData['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $path = $resData['file']->store('session_resources', 'public');
                        $session->resources()->create([
                            'nm_resource' => $resData['nm_resource'],
                            'type' => $resData['type'],
                            'file_path' => $path,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Session updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update session: ' . $e->getMessage()])->withInput();
        }
    }

    public function storeActivity(Request $request, RpsSession $session)
    {
        $request->validate([
            'type' => 'required|in:Connect,Coach,Check,Wrap-up',
            'duration' => 'required|integer|min:1',
            'content' => 'required|string',
        ]);

        $session->activities()->create($request->all());

        return redirect()->back()->with('success', 'Activity added successfully.');
    }

    public function destroyActivity(RpsActivity $activity)
    {
        $activity->delete();
        return redirect()->back()->with('success', 'Activity deleted successfully.');
    }

    public function exportPdf(Rps $rp)
    {
        $rp->load([
            'subject.prodi.fakultas', 
            'subject.prerequisites', 
            'subject.plos', 
            'subject.clos', 
            'subject.bks', 
            'sessions.activities', 
            'sessions.clos',
            'sessions.assessments',
            'sessions.assessments.clo'
        ]);
        
        // Fetch Visi, Misi, Tujuan
        $prodi = $rp->subject->prodi;
        $fakultas = $prodi ? $prodi->fakultas : null;
        $univ = Univ::first(); // Assuming one university
        
        $visiUniv = $univ ? $univ->visi()->with('details')->first() : null;
        $visiFakultas = $fakultas ? $fakultas->visi()->with('details')->first() : null;
        $visiProdi = $prodi ? $prodi->visi()->with('details')->first() : null;

        $pdf = Pdf::loadView('admin.rps.pdf', compact('rp', 'visiUniv', 'visiFakultas', 'visiProdi'))->setPaper('a4', 'landscape');
        return $pdf->download('RPS_' . ($rp->subject->kode_subject ?? 'Unknown') . '.pdf');
    }
}
