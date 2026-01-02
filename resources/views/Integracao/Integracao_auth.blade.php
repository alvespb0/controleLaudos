@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <h3 class="card-title mb-4 text-center text-dark">
                <i class="bi bi-shield-lock"></i> Autenticação da Integração
            </h3>
            
            <div class="alert alert-info mb-4">
                <strong>Sistema:</strong> {{ $integracao->sistema }}<br>
                <strong>Tipo de Autenticação:</strong> 
                <span class="badge bg-primary">{{ strtoupper($integracao->auth) }}</span>
                <span class="badge bg-secondary ms-2">{{ strtoupper($integracao->tipo) }}</span>
            </div>

            <form action="{{route('auth.integracao.update', $integracao->id)}}" method="POST">
                @csrf
                @if($integracao->auth == 'basic')
                    <div class="mb-3">
                        <i class="bi bi-person"></i>
                        <label for="username" class="form-label">&nbspUsuário</label>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Nome de usuário para autenticação básica" 
                               value="{{ old('username', $integracao->username ?? '') }}"
                               required>
                        <small class="form-text text-muted">Usuário para autenticação HTTP Basic</small>
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-key"></i>
                        <label for="password" class="form-label">&nbspSenha</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Senha para autenticação básica"
                                   value="{{ old('password') }}">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            @if($integracao->password_enc)
                                <span class="text-success"><i class="bi bi-check-circle"></i> Senha já configurada. Deixe em branco para manter a atual.</span>
                            @else
                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Configure uma senha para esta integração.</span>
                            @endif
                        </small>
                    </div>
                @elseif($integracao->auth == 'bearer')
                    <div class="mb-3">
                        <i class="bi bi-key-fill"></i>
                        <label for="token" class="form-label">&nbspToken Bearer</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="token" 
                                   name="password" 
                                   placeholder="Token de autenticação Bearer"
                                   value="{{ old('password') }}">
                            <button class="btn btn-outline-secondary" type="button" id="toggleToken">
                                <i class="bi bi-eye" id="eyeIconToken"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            @if($integracao->password_enc)
                                <span class="text-success"><i class="bi bi-check-circle"></i> Token já configurado. Deixe em branco para manter o atual.</span>
                            @else
                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Configure um token para esta integração.</span>
                            @endif
                        </small>
                    </div>
                @elseif($integracao->auth == 'wss')
                    <div class="mb-3">
                        <i class="bi bi-person"></i>
                        <label for="username" class="form-label">&nbspUsuário WSS</label>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Nome de usuário para WebSocket Secure" 
                               value="{{ old('username', $integracao->username ?? '') }}"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-key"></i>
                        <label for="password" class="form-label">&nbspSenha WSS</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Senha para WebSocket Secure"
                                   value="{{ old('password') }}">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            @if($integracao->password_enc)
                                <span class="text-success"><i class="bi bi-check-circle"></i> Senha já configurada. Deixe em branco para manter a atual.</span>
                            @else
                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Configure uma senha para esta integração.</span>
                            @endif
                        </small>
                    </div>
                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{route('read.integracao')}}" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Salvar Autenticação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    // Toggle token visibility
    document.getElementById('toggleToken')?.addEventListener('click', function() {
        const tokenInput = document.getElementById('token');
        const eyeIconToken = document.getElementById('eyeIconToken');
        
        if (tokenInput.type === 'password') {
            tokenInput.type = 'text';
            eyeIconToken.classList.remove('bi-eye');
            eyeIconToken.classList.add('bi-eye-slash');
        } else {
            tokenInput.type = 'password';
            eyeIconToken.classList.remove('bi-eye-slash');
            eyeIconToken.classList.add('bi-eye');
        }
    });
</script>
@endsection

