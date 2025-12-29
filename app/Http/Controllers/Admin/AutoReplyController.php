<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoReply;
use Illuminate\Http\Request;

class AutoReplyController extends Controller
{
    /**
     * Listagem das respostas automáticas
     */
    public function index()
    {
        $autoReplies = AutoReply::orderBy('priority')
            ->orderBy('id')
            ->get();

        return view('admin.auto-replies.index', compact('autoReplies'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        return view('admin.auto-replies.create');
    }

    /**
     * Guardar nova resposta automática
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trigger'  => ['required', 'string', 'max:255'],
            'response' => ['required', 'string'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'active'   => ['nullable', 'boolean'],
        ]);

        AutoReply::create([
            'trigger'  => mb_strtolower(trim($validated['trigger'])),
            'response' => trim($validated['response']),
            'priority' => $validated['priority'] ?? 0,
            'active'   => $request->boolean('active'),
        ]);

        return redirect()
            ->route('admin.auto-replies.index')
            ->with('success', 'Resposta automática criada com sucesso.');
    }

    /**
     * Formulário de edição
     */
    public function edit(AutoReply $autoReply)
    {
        return view('admin.auto-replies.edit', compact('autoReply'));
    }

    /**
     * Atualizar resposta automática
     */
    public function update(Request $request, AutoReply $autoReply)
    {
        $validated = $request->validate([
            'trigger'  => ['required', 'string', 'max:255'],
            'response' => ['required', 'string'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'active'   => ['nullable', 'boolean'],
        ]);

        $autoReply->update([
            'trigger'  => mb_strtolower(trim($validated['trigger'])),
            'response' => trim($validated['response']),
            'priority' => $validated['priority'] ?? 0,
            'active'   => $request->boolean('active'),
        ]);

        return redirect()
            ->route('admin.auto-replies.index')
            ->with('success', 'Resposta automática atualizada com sucesso.');
    }

    /**
     * Remover resposta automática
     */
    public function destroy(AutoReply $autoReply)
    {
        $autoReply->delete();

        return redirect()
            ->route('admin.auto-replies.index')
            ->with('success', 'Resposta automática removida com sucesso.');
    }
}
