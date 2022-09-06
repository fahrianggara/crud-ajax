<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employee.index');
    }

    public function employeeTable()
    {
        $emps = Employee::all();
        $output = '';
        if ($emps->count() > 0) {
            $output .= '
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>';
            $no = 1;
            foreach ($emps as $emp) {
                $output .= '
                <tr>
                    <td>' . $no++ . '</td>
                    <td><img src="assets/img/employee/' . $emp->image . '" alt="Profile ' . $emp->name . '"></td>
                    <td>' . $emp->name . '</td>
                    <td>' . $emp->phone . '</td>
                    <td>
                        <button type="button" value="' . $emp->id . '" class="edit_btn btn btn-warning"><i
                                class="uil uil-pen"></i>
                        </button>
                        <button type="button" value="' . $emp->id . '" class="del_btn btn btn-danger"><i
                                class="uil uil-trash"></i>
                        </button>
                    </td>
                </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo "<p class='text-muted text-center'>no record data employee!</p>";
        }
    }

    // function Insert data
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            "name" => "alpha_spaces|required|max:20|min:3",
            "phone" => "required|numeric",
            "image" => "required|image|mimes:png,jpg,jpeg|max:1024"
        ]);

        // Pengecekan Validation
        if ($validator->fails()) {
            // Error Messages
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            $employee = new Employee;
            // Process insert
            $employee->name = $request->input('name');
            $employee->phone = $request->input('phone');

            // pengecekan insert image
            if ($request->hasFile('image')) {
                // Costume image name
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = uniqid("EMP-", true) . '.' . $ext;
                // Upload image to folder image
                $file->move("assets/img/employee/", $filename);
                // Process insert image
                $employee->image = $filename;
            }

            // Insert Data
            $employee->save();

            // Success Messages
            return response()->json([
                'status' => 200,
                'message' => "New Employee data Saved successfully!",
            ]);
        }
    }

    // Show Edit data employee
    public function edit($id)
    {
        $employee = Employee::find($id);

        if ($employee) {
            return response()->json([
                'status' => 200,
                'dataEmployee' => $employee
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "Data employe not found!"
            ]);
        }
    }

    // Updating data employee
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            "name" => "alpha_spaces|max:20|min:3",
            "phone" => "required|numeric",
            "image" => "image|mimes:png,jpg,jpeg|max:1024"
        ]);

        // Pengecekan Validation
        if ($validator->fails()) {
            // Error Messages
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            $employee = Employee::find($id);

            if ($employee) {
                // Process insert
                $employee->name = $request->input('name');
                $employee->phone = $request->input('phone');

                // pengecekan insert image
                if ($request->hasFile('image')) {
                    $path = "assets/img/employee/" . $employee->image;
                    // Pengecekan hapus gambar lama diganti baru
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    // Costume image name
                    $file = $request->file('image');
                    $ext = $file->getClientOriginalExtension();
                    $filename = uniqid("EMP-", true) . '.' . $ext;
                    // Upload image to folder image
                    $file->move("assets/img/employee/", $filename);
                    // Process insert image
                    $employee->image = $filename;
                }

                // Pengecekan jika tidak ada yang mau diupdate maka ada notif tidak di update, sebaliknya juga
                if ($employee->isDirty()) {
                    // Insert Data
                    $employee->update();
                    // Success Messages
                    return response()->json([
                        'status' => 200,
                        'message' => "Employee data with $employee->name's name, Updated successfully!",
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'message' => "Oops.. nothing seems to be updated!",
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "Data employe not found!"
                ]);
            }
        }
    }

    public function delete($id)
    {
        $employee = Employee::find($id);

        if ($employee) {
            // PENGECEKAN, JIKA DIHAPUS DATANYA MAKA FILE IMAGE NYA JUGA DIHAPUS
            $path = "assets/img/employee/" . $employee->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            // PROSES DELETING
            $employee->delete();

            return response()->json([
                'status' => 200,
                'message' => "Employee data with $employee->name's name, Deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "Data employee not found!"
            ]);
        }
    }
}
