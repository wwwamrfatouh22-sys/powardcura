<?php

namespace App\Http\Controllers;

use App\Http\Requests\ElectronicSignatureRequest;
use App\Models\Document;
use App\Models\DocumentSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ElectronicSignatureController extends Controller
{
    public function show(Document $document)
    {
        $doctor = Auth::guard('doctor')->user();

        if (!$doctor) {
            abort(403);
        }

        $existingSignature = DocumentSignature::where('doctor_id', $doctor->id)->where('document_id', $document->id)->first();

        return view('doctors.electronic-signature', compact('document', 'existingSignature'));
    }

    public function store(ElectronicSignatureRequest $request)
    {
        $doctor = Auth::guard('doctor')->user();
        $data = $request->validated();

        DocumentSignature::updateOrCreate(
            [
                'doctor_id' => $doctor->id,
                'document_id' => $data['document_id'],
            ],
            [
                'signature' => $data['signature'],
            ]
        );

        return back()->with('success', 'Signature submitted successfully!');
    }
    public function redirectToLatest()
    {
        $document = Document::latest()->first();

        if (!$document) {
            abort(404);
        }

        return redirect()->route('signature.show', ['document' => $document->id]);
    }
}
