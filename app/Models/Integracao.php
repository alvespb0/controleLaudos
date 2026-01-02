<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class Integracao extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'integracoes';

    /**
     * Campos permitidos para mass assignment
     */
    protected $fillable = [
        'sistema',
        'descricao',
        'endpoint',
        'slug',
        'username',
        'password_enc',
        'auth', # enum basic, bearer, wss
        'tipo', # enum soap, rest
    ];

    /**
     * Descriptografa a senha armazenada
     * 
     * @return string|null Retorna a senha descriptografada ou null se não houver senha ou se houver erro
     */
    public function getDecryptedPassword(): ?string
    {
        if (empty($this->password_enc)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->password_enc);
        } catch (DecryptException $e) {
            // Se a chave APP_KEY mudou ou o valor foi corrompido, retorna null
            // Você pode logar o erro se necessário
            \Log::warning("Erro ao descriptografar senha da integração ID {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Accessor para obter a senha descriptografada
     * Uso: $integracao->password_decrypted
     */
    public function getPasswordDecryptedAttribute(): ?string
    {
        return $this->getDecryptedPassword();
    }
}
