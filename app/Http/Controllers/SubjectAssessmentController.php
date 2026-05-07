<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectAssessment;
use Illuminate\Http\Request;
use Exception;

class SubjectAssessmentController extends Controller
{
    public function manage(Subject $subject)
    {
        $subject->load('assessments');
        return view('admin.subject_assessments.manage', compact('subject'));
    }

    public function store(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:0|max:100',
            'rubric_link' => 'nullable|string|url',
        ]);

        try {
            // Cek apakah total bobot melebihi 100%
            $currentTotal = $subject->assessments()->sum('weight');
            if ($currentTotal + $request->weight > 100) {
                return redirect()->back()->withErrors(['error' => 'Total weight cannot exceed 100%. Current total: ' . $currentTotal . '%'])->withInput();
            }

            SubjectAssessment::create([
                'subject_id' => $subject->id,
                'name' => $request->name,
                'weight' => $request->weight,
                'rubric_link' => $request->rubric_link,
            ]);

            return redirect()->back()->with('success', 'Assessment added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add assessment: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, SubjectAssessment $assessment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:0|max:100',
            'rubric_link' => 'nullable|string|url',
        ]);

        try {
            $subject = $assessment->subject;
            $currentTotal = $subject->assessments()->where('id', '!=', $assessment->id)->sum('weight');
            
            if ($currentTotal + $request->weight > 100) {
                return redirect()->back()->withErrors(['error' => 'Total weight cannot exceed 100%. Current total of other assessments: ' . $currentTotal . '%']);
            }

            $assessment->update([
                'name' => $request->name,
                'weight' => $request->weight,
                'rubric_link' => $request->rubric_link,
            ]);

            return redirect()->back()->with('success', 'Assessment updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update assessment: ' . $e->getMessage()]);
        }
    }

    public function destroy(SubjectAssessment $assessment)
    {
        try {
            $assessment->delete();
            return redirect()->back()->with('success', 'Assessment deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to delete assessment.')]);
        }
    }
}
