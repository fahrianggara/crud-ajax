<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('teacher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = new Teacher();
        return view('teacher.form.form', compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $this->validate($request, [
            "name" => "max:20|min:3|string",
            "address" => "",
            "image" => "image|mimes:png,jpg,jpeg|max:1024",
        ]);

        $teacher = new Teacher;
        // Process insert
        $teacher->name = $request->input('name');
        $teacher->address = $request->input('address');

        // pengecekan insert image
        if ($request->hasFile('image')) {
            // Costume image name
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = uniqid("TCHR-", true) . '.' . $ext;
            // Upload image to folder image
            $file->move("assets/img/teacher/", $filename);
            // Process insert image
            $teacher->image = $filename;
        }

        // Insert Data
        $teacher->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        //
    }

    public function dataTable()
    {
        $model = Teacher::query();

        return DataTables::of($model)
            ->addColumn('action', function ($model) {
                return view('teacher.options.action', [
                    'model' => $model,
                    'url_show' => route('teacher.show', $model->id),
                    'url_edit' => route('teacher.edit', $model->id),
                    'url_destroy' => route('teacher.destroy', $model->id),
                ]);
            })
            ->addIndexColumn()
            ->rawColumns(['actions'])
            ->make(true);
    }
}
