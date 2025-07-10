<?php

namespace Modules\StudyPlan\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\Group\Models\Group;
use Modules\StudyPlan\Models\LessonSlot;

class StudyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = Group::orderBy('title')->get();
        $selectedGroupId = $request->get('group_id');
        $selectedDate = $request->get('date');

        $query = LessonSlot::with(['group', 'subject', 'teacher'])->orderBy('date')->orderBy('slot_order');

        if ($selectedGroupId) {
            $query->where('group_id', $selectedGroupId);
        }

        if ($selectedDate) {
            $query->whereDate('date', $selectedDate);
        }

        $slots = $query->get()
            ->groupBy('date');

        return view('studyplan::lessons.slots', compact(
            'groups',
            'selectedGroupId',
            'selectedDate',
            'slots'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('studyplan::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('studyplan::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('studyplan::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
