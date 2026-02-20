<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * List documents ikut role:
     * - admin   : semua
     * - manager : public + department sendiri
     * - staff   : public sahaja
     */
    public function index(Request $request)
    {
        // JANGAN authorize viewAny kat sini – Afdzal akan handle sendiri ikut role
        $user = $request->user();
        $role = strtolower((string) $user->role);

        $query = Document::query()
            ->with(['category', 'department', 'uploader'])
            ->latest();

        // Admin: semua dokumen
        if ($role === 'admin') {
            return DocumentResource::collection($query->get());
        }

        // Manager: public + department sendiri
        if ($role === 'manager') {
            $query->where(function ($q) use ($user) {
                $q->where('access_level', 'public')
                  ->orWhere('department_id', $user->department_id);
            });

            return DocumentResource::collection($query->get());
        }

        // Staff / employee: public sahaja
        $query->where('access_level', 'public');

        return DocumentResource::collection($query->get());
    }

    /**
     * Show single document.
     * Akan check policy: DocumentPolicy@view
     */
    public function show(Request $request, Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['category', 'department', 'uploader']);

        return new DocumentResource($document);
    }

    /**
     * Store document.
     * Akan check policy: DocumentPolicy@create
     * Manager hanya boleh upload untuk department sendiri (extra rule).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Document::class);

        $user = $request->user();
        $role = strtolower((string) $user->role);

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'department_id' => 'required|integer',
            'category_id'   => 'required|integer',
            'access_level'  => 'required|in:public,private',
            'file'          => 'required|file|max:10240', // 10MB
        ]);

        // extra rule: manager hanya boleh upload dept sendiri
        if ($role === 'manager' && (int) $data['department_id'] !== (int) $user->department_id) {
            return response()->json([
                'message' => 'Manager hanya boleh upload dokumen untuk department sendiri.',
            ], 403);
        }

        // simpan file & metadata
        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $doc = Document::create([
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'department_id' => $data['department_id'],
            'category_id'   => $data['category_id'],
            'access_level'  => $data['access_level'],

            'file_name'     => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_type'     => $file->getClientMimeType(),
            'file_size'     => $file->getSize(),

            'uploaded_by'   => $user->id,
            'download_count'=> 0,
        ]);

        $doc->load(['category', 'department', 'uploader']);

        return (new DocumentResource($doc))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update document.
     * Akan check policy: DocumentPolicy@update
     */
    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $data = $request->validate([
            'title'         => 'sometimes|required|string|max:255',
            'description'   => 'sometimes|nullable|string',
            'department_id' => 'sometimes|required|integer',
            'category_id'   => 'sometimes|required|integer',
            'access_level'  => 'sometimes|required|in:public,private',
            'file'          => 'sometimes|file|max:10240',
        ]);

        // kalau update file: buang lama, simpan baru
        if ($request->hasFile('file')) {
            if (!empty($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            $document->file_name = $file->getClientOriginalName();
            $document->file_path = $path;
            $document->file_type = $file->getClientMimeType();
            $document->file_size = $file->getSize();
        }

        // update field lain
        foreach (['title', 'description', 'department_id', 'category_id', 'access_level'] as $field) {
            if (array_key_exists($field, $data)) {
                $document->{$field} = $data[$field];
            }
        }

        $document->save();
        $document->load(['category', 'department', 'uploader']);

        return new DocumentResource($document);
    }

    /**
     * Delete document.
     * Akan check policy: DocumentPolicy@delete
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        if (!empty($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'message' => 'Document deleted.',
        ], 200);
    }

    /**
     * Download document file.
     * Akan check policy: DocumentPolicy@download
     */
    public function download(Document $document)
    {
        $this->authorize('download', $document);

        if (empty($document->file_path)) {
            return response()->json([
                'message' => 'File tak wujud untuk dokumen ini.',
            ], 404);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($document->file_path)) {
            return response()->json([
                'message' => 'File tidak dijumpai dalam storage.',
            ], 404);
        }

        // increment download count
        $document->increment('download_count');

        // bagi nama file kemas – fallback original nama file
        $downloadName = $document->file_name ?: 'document';

        return $disk->download($document->file_path, $downloadName);
    }
}