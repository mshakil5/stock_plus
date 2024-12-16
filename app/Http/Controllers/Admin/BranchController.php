<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\BranchDetails;
use App\Mail\SendBranchEmail;
use Illuminate\Support\Facades\Mail;

class BranchController extends Controller
{
  public function get_all_branch()
  {
    $branch = Branch::all();


    return Response::json($branch);
  }


  public function save_branch(Request $request)
  {
    $branch = new Branch();
    $branch->name = $request->branch;
    $branch->created_by = Auth::user()->id;
    $branch->save();
    return;
  }

  public function view_branch(Request $request)
  {

    if ($request->ajax()) {
      $branch = Branch::with('branchDetails')->get();

      return Datatables::of($branch)
          ->addColumn('has_details', function ($branch) {
              return $branch->branchDetails ? true : false;
          })
          ->make(true);
  }
    return view("admin.branch.index");
  }

  public function published_branch($ID)
  {

    Branch::where('id', $ID)
      ->update(['status' => 1]);

    return;
  }


  public function unpublished_branch($ID)
  {
    Branch::where('id', $ID)
      ->update(['status' => 0]);

    return;
  }

  public function edit_branch(Request $request, $id)
  {
    $branch = Branch::where('id', $id)
      ->update([
        'name' => $request['data']['branchName'],
        'invoice_format' => $request['data']['invoiceFormat'],
        'quotation_format' => $request['data']['quotationFormat']
      ]);

    return;
  }

  public function showDetails($id)
  {
    $branch = Branch::findOrFail($id);
    $branchDetails = $branch->branchDetails;
    return view('admin.branch.details', compact('branch', 'branchDetails'));
  }

  public function branchDetailsStore(Request $request)
  {
    $validated = $request->validate([
      'branch_id' => 'required',
      'branch_name' => 'required|string|max:191',
      'email1' => 'nullable|email|max:191',
      'email2' => 'nullable|email|max:191',
      'phone1' => 'nullable|string|max:191',
      'phone2' => 'nullable|string|max:191',
      'address' => 'nullable|string|max:191',
      'google_map' => 'nullable|string',
      'fav_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'branch_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'invoice_header' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $data = new BranchDetails();
    $data->branch_id = $request->branch_id;
    $data->branch_name = $request->branch_name;
    $data->email1 = $request->email1;
    $data->email2 = $request->email2;
    $data->phone1 = $request->phone1;
    $data->phone2 = $request->phone2;
    $data->address = $request->address;
    $data->google_map = $request->google_map;

    if ($request->hasFile('fav_icon')) {
      $favIconName = rand(100000, 999999) . '_fav_icon.' . $request->fav_icon->extension();
      $request->fav_icon->move(public_path('images/branch'), $favIconName);
      $data->fav_icon = $favIconName;
    }

    if ($request->hasFile('branch_logo')) {
      $logoName = rand(100000, 999999) . '_branch_logo.' . $request->branch_logo->extension();
      $request->branch_logo->move(public_path('images/branch'), $logoName);
      $data->branch_logo = $logoName;
    }

    if ($request->hasFile('invoice_header')) {
      $logoName = rand(100000, 999999) . '_invoice_header.' . $request->invoice_header->extension();
      $request->invoice_header->move(public_path('images/branch'), $logoName);
      $data->invoice_header = $logoName;
    }

    $data->save();

    return redirect()->back()->with('success', 'Branch details saved successfully!');
  }

  public function branchDetailsUpdate(Request $request, $id)
  {
    $validated = $request->validate([
      'branch_id' => 'required',
      'branch_name' => 'required|string|max:191',
      'email1' => 'nullable|email|max:191',
      'email2' => 'nullable|email|max:191',
      'phone1' => 'nullable|string|max:191',
      'phone2' => 'nullable|string|max:191',
      'address' => 'nullable|string|max:191',
      'google_map' => 'nullable|string',
      'fav_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'branch_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $data = BranchDetails::findOrFail($id);
    $data->branch_name = $request->branch_name;
    $data->email1 = $request->email1;
    $data->email2 = $request->email2;
    $data->phone1 = $request->phone1;
    $data->phone2 = $request->phone2;
    $data->address = $request->address;
    $data->google_map = $request->google_map;

    if ($request->hasFile('fav_icon')) {
      if ($data->fav_icon && file_exists(public_path('images/branch/' . $data->fav_icon))) {
        unlink(public_path('images/branch/' . $data->fav_icon));
      }
      $favIconName = rand(100000, 999999) . '_fav_icon.' . $request->fav_icon->extension();
      $request->fav_icon->move(public_path('images/branch'), $favIconName);
      $data->fav_icon = $favIconName;
    }

    if ($request->hasFile('branch_logo')) {
      if ($data->branch_logo && file_exists(public_path('images/branch/' . $data->branch_logo))) {
        unlink(public_path('images/branch/' . $data->branch_logo));
      }
      $branchLogoName = rand(100000, 999999) . '_branch_logo.' . $request->branch_logo->extension();
      $request->branch_logo->move(public_path('images/branch'), $branchLogoName);
      $data->branch_logo = $branchLogoName;
    }

    if ($request->hasFile('invoice_header')) {
      if ($data->invoice_header && file_exists(public_path('images/branch/' . $data->invoice_header))) {
        unlink(public_path('images/branch/' . $data->invoice_header));
      }
      $invoiceHeaderName = rand(100000, 999999) . '_invoice_header.' . $request->invoice_header->extension();
      $request->invoice_header->move(public_path('images/branch'), $invoiceHeaderName);
      $data->invoice_header = $invoiceHeaderName;
    }

    $data->save();

    return redirect()->back()->with('success', 'Branch details updated successfully!');
  }

  public function sendMail($branch_id)
  {
      if (!$branch_id) {
          return redirect()->back()->with('error', 'Branch not found.');
      }

      $branch = BranchDetails::where('branch_id', $branch_id)->select('email1', 'branch_name')->first();

      if(!$branch){
        return redirect()->back()->with('error', 'Branch mail not found.');
      }

      $branchEmail = $branch->email1;
      $branchName = $branch->branch_name;

      return view('admin.branch.send_mail', compact('branchEmail', 'branchName'));
  }

    public function sendEmailStore(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'email' => 'required|email'
        ]);

        Mail::to($validated['email'])->send(new SendBranchEmail($validated['subject'], $validated['body']));

        return response()->json([
            'status' => 'success',
            'message' => 'Email sent successfully!'
        ]);
    }
}
