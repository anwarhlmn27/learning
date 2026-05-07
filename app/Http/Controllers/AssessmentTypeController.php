<?php

namespace App\Http\Controllers;

use App\Models\AssessmentType;
use Illuminate\Http\Request;

class AssessmentTypeController extends Controller
{
    public function index()
    {
        $types = AssessmentType::latest()->get();
        return view('admin.assessment_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:assessment_types,name|max:255',
        ]);

        AssessmentType::create($request->only('name'));
        return redirect()->back()->with('success', 'Assessment Type created successfully.');
    }

    public function update(Request $request, AssessmentType $assessmentType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:assessment_types,name,' . $assessmentType->id,
        ]);

        $assessmentType->update($request->only('name'));
        return redirect()->back()->with('success', 'Assessment Type updated successfully.');
    }

    public function destroy(AssessmentType $assessmentType)
    {
        $assessmentType->delete();
        return redirect()->back()->with('success', 'Assessment Type deleted successfully.');
    }
}
