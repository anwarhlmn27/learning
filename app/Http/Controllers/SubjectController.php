<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Exception;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('prerequisite')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $assessmentTypes = $this->getAssessmentTypes();
        return view('admin.subjects.create', compact('subjects', 'assessmentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_subject' => 'required|string|unique:subjects,kode_subject',
            'nama_subject' => 'required|string|max:255',
            'sks_t' => 'required|integer|min:0',
            'sks_p' => 'required|integer|min:0',
            'total_sks' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:14',
            'assesment_type' => 'required|in:' . implode(',', $this->getAssessmentTypes()),
            'prerequisite_id' => 'nullable|exists:subjects,id',
        ]);

        try {
            Subject::create($request->all());
            return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create subject: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Subject $subject)
    {
        $subjects = Subject::where('id', '!=', $subject->id)->get();
        $assessmentTypes = $this->getAssessmentTypes();
        return view('admin.subjects.edit', compact('subject', 'subjects', 'assessmentTypes'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'kode_subject' => 'required|string|unique:subjects,kode_subject,' . $subject->id,
            'nama_subject' => 'required|string|max:255',
            'sks_t' => 'required|integer|min:0',
            'sks_p' => 'required|integer|min:0',
            'total_sks' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:14',
            'assesment_type' => 'required|in:' . implode(',', $this->getAssessmentTypes()),
            'prerequisite_id' => 'nullable|exists:subjects,id|different:id',
        ]);

        try {
            $subject->update($request->all());
            return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update subject: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            // Check if this subject is a prerequisite for others
            if ($subject->dependents()->count() > 0) {
                return redirect()->back()->withErrors(['error' => 'Cannot delete subject because it is a prerequisite for other subjects.']);
            }
            
            $subject->delete();
            return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete subject: ' . $e->getMessage()]);
        }
    }

    private function getAssessmentTypes()
    {
        return [
            'Project', 'Prototype', 'Coding', 'Design Project', 
            'Essay', 'Presentation', 'Case Study', 'SQL Lab', 'Quiz'
        ];
    }
}
