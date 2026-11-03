<?php

use App\Models\Block;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** O texto longo era um placeholder; agora é o rótulo do campo. Só troca se não foi editado no admin. */
    public function up(): void
    {
        $old = 'Informe o motivo solictação de retorno de contato';

        Block::where('type', 'contact_form')->get()->each(function (Block $block) use ($old) {
            $data = $block->data;
            if (($data['message']['pt'] ?? null) === $old) {
                $data['message'] = ['pt' => 'Mensagem', 'en' => 'Message', 'es' => 'Mensaje'] + $data['message'];
                $block->update(['data' => $data]);
            }
        });
    }

    public function down(): void
    {
        //
    }
};
