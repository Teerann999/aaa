<?php

namespace App\Http\Controllers;

use App\Repositories\CategorieRepository;
use App\Repositories\DocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        if(strtolower(Auth::user()->user_type) !== 'admin'){
            return redirect('/');
        }

        return view('document.index');
    }

    public function main()
    {
        return view('document.main');
    }

    public function getDatatables(DocumentRepository $document)
    {
        $data = $document->getDatatables();
        $datatables = app('datatables');
        return $datatables->eloquent($data)
            ->addColumn('categorie_name', function ($value) {
                return $value->categorie->name;
            })
            ->make(true);
    }

    public function renderForm(DocumentRepository $document, CategorieRepository $categorie, $id)
    {
        if(strtolower(Auth::user()->user_type) !== 'admin'){
            return redirect('/');
        }
        
        $cate = $document->getById($id);
        $data['title'] = !empty($cate) ? $cate['name'] : 'สร้างเอกสาร';
        $data['data'] = !empty($cate) ? $cate : $document->castData();
        $data['categories'] = $categorie->getAll();
        return view('document.partials.form', $data);
    }

    public function view(DocumentRepository $document, $id)
    {        
        $cate = $document->getById($id);
        $data['title'] = $cate['name'];
        $data['data'] =  $cate;        
        return view('document.partials.view', $data);
    }

    public function store(DocumentRepository $document, Request $request)
    {
        $result = $document->store($request);
        return response()->json([
            'message' => 'บันทึกข้อมูลสำเร็จ',
            'status' => 'success',
        ], 200);
    }

    public function update(DocumentRepository $document, Request $request, $id)
    {
        $result = $document->update($request, $id);
        $message = $result ? 'บันทึกข้อมูลสำเร็จ' : 'เกิดข้อผิดพลาด!';
        $status = $result ? 'success' : 'error';
        return response()->json([
            'message' => $message,
            'status' => $status,
        ], 200);
    }

    public function delete(DocumentRepository $document, $id)
    {
        $result = $document->delete($id);
        $message = $result ? 'ลบข้อมูลสำเร็จ' : 'เกิดข้อผิดพลาด!';
        $status = $result ? 'success' : 'error';
        return response()->json([
            'message' => $message,
            'status' => $status,
        ], 200);
    }

    public function uploadfile(Request $request)
    {
        $validator = Validator::make($request->all(), ['file' => 'required|mimes:pdf|max:10240'],
        [
            'file.mimes' => 'ไฟล์เอกสารต้องเป็น PDF เท่านั้น',
            'file.required' => 'ระบุไฟล์เพื่ออัพโหลด',
            'file.max' => 'ขนาดไฟล์ใหญ่เกินไป ห้ามเกิน 10 MB.'
        ]);
        $status = 'success';
        $message = 'อัพโหลดเสร็จสมบูรณ์';
        $data = null;
        if ($validator->fails()) {
            $status = 'error';
            $message = $validator->errors();
        } else {
            $data = $request->file('file')->store('documents');
        }

        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data
        ], 200);
    }
}
