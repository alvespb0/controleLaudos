@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none;">
        <div class="card-body p-4">
            <!-- <h3 class="card-title mb-4 text-center text-dark">Escolha o Tipo de Orçamento</h3> -->

                @csrf

                <!-- Tipo de orçamento -->
                <div class="mb-3">
                    <label class="form-label d-block">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        &nbsp {{$fileName}} Gerado com sucesso!
                    </label>

                    <form method="GET" action="{{ route('orcamento.aprovar', $fileName) }}" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-success me-2" style="background-color: var(--primary-color); border: none;">Aprovar</button>
                    </form>

                    <form method="POST" action="{{ route('orcamento.retificar') }}" style="display:inline-block;">
                        @csrf
                        @foreach ($dados as $key => $value)
                            <input type="hidden" name="dados[{{ $key }}]" value="{{ $value }}">
                        @endforeach
                        <input type="hidden" name="fileName" value="{{$fileName}}">
                        <button type="submit" class="btn btn-warning me-2" style="background-color: var(--accent-color); color: #fff; border: none;">Retificar</button>
                    </form>

                    <button id="downloadBtn" class="btn btn-primary" style="background-color: var(--secondary-color); border: none;" onclick="handleDownload(event)">Download</button>
                    <div id="downloadAlert" class="mt-2 text-danger fw-bold" style="display:none; font-size: 0.95rem;">
                        O download só pode ser realizado uma vez. Caso precise novamente, gere um novo orçamento.
                    </div>

                    <form id="downloadForm" action="{{ route('orcamento.download', $fileName) }}" method="get" target="_blank" style="display:none;"></form>
                </div>
        </div>
    </div>
</div>

<script>
    function handleDownload(e) {
        e.preventDefault();
        const btn = document.getElementById('downloadBtn');
        btn.disabled = true;
        btn.innerText = 'Download realizado';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
        document.getElementById('downloadAlert').style.display = 'block';
        document.getElementById('downloadForm').submit();
    }
</script>

@endsection
