@extends('templateMain')

@section('head')
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <!-- FullCalendar Locale -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/pt-br.global.min.js"></script>
    <style>
        .fc {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .fc-header-toolbar {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            margin: 0;
            border-radius: 10px 10px 0 0;
        }
        
        .fc-button {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
        }
        
        .fc-button:hover {
            background-color: var(--hover-color) !important;
            border-color: var(--hover-color) !important;
        }
        
        .fc-button:focus {
            box-shadow: 0 0 0 0.2rem rgba(67, 124, 144, 0.25) !important;
        }
        
        .fc-button-active {
            background-color: var(--accent-color) !important;
            border-color: var(--accent-color) !important;
            color: var(--secondary-color) !important;
        }
        
        .fc-today {
            background-color: #e3f2fd !important;
        }
        
        .fc-daygrid-event {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        .fc-event-title {
            font-weight: 500;
        }
        
        .fc-daygrid-day-number {
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .fc-col-header-cell {
            background-color: var(--light-color);
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .fc-daygrid-day:hover {
            background-color: #f8f9fa !important;
        }
        
        .calendar-container {
            background-color: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .calendar-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 10px 10px 0 0;
            margin: -1rem -1rem 1rem -1rem;
        }
        
        .event-modal .modal-content {
            border-radius: 10px;
        }
        
        .event-modal .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="calendar-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-calendar3 me-2"></i>Calendário Google
                    </h1>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-light" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Atualizar
                        </button>
                        <button class="btn btn-light" onclick="showInstructions()">
                            <i class="bi bi-calendar-plus me-1"></i>Novo Evento
                        </button>
                        <a href="https://calendar.google.com" target="_blank" class="btn btn-light">
                            <i class="bi bi-calendar3 me-1"></i>Abrir Google Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="calendar-container">
                <div id="calendar" style="min-height: 600px; background-color: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <i class="bi bi-hourglass-split display-4 text-muted"></i>
                        <p class="mt-2 text-muted">Carregando calendário...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar com Informações -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-check-circle me-2"></i>Status da Conta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-google text-primary me-2" style="font-size: 1.5rem;"></i>
                        <div>
                            <h6 class="mb-1">Google Calendar</h6>
                            <small class="text-success">
                                <i class="bi bi-check-circle-fill me-1"></i>Conectado
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informações
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Eventos carregados:</strong> {{ $events->getItems() ? count($events->getItems()) : 0 }}
                    </p>
                    <p class="mb-2">
                        <strong>Última atualização:</strong> {{ now()->format('d/m/Y H:i') }}
                    </p>
                    <p class="mb-0">
                        <strong>Próximos 10 eventos</strong> da sua agenda principal.
                    </p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Dicas
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-arrow-right text-primary me-2"></i>
                            Clique nos eventos para ver detalhes
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-arrow-right text-primary me-2"></i>
                            Clique em um dia para ver a semana
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-arrow-right text-primary me-2"></i>
                            Arraste e solte para selecionar horário
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-arrow-right text-primary me-2"></i>
                            Mude a visualização (mês/semana/dia)
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalhes do evento -->
<div class="modal fade event-modal" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Detalhes do Evento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="https://calendar.google.com" target="_blank" class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-1"></i>Abrir no Google Calendar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para criar evento -->
<div class="modal fade" id="createEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus me-2"></i>Novo Evento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('calendar.createEvent') }}" method="POST" id="createEventForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="summary" class="form-label">Título do Evento *</label>
                        <input type="text" class="form-control" id="summary" name="summary" required placeholder="Digite o título do evento...">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Início</label>
                                <div class="form-control" id="startDisplay" style="background-color: #f8f9fa;">
                                    <!-- Será preenchido via JavaScript -->
                                </div>
                                <input type="hidden" id="start" name="start">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Fim</label>
                                <div class="form-control" id="endDisplay" style="background-color: #f8f9fa;">
                                    <!-- Será preenchido via JavaScript -->
                                </div>
                                <input type="hidden" id="end" name="end">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Período selecionado:</strong> Arraste e solte no calendário para selecionar o horário desejado.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="createEventBtn" disabled>
                        <i class="bi bi-calendar-plus me-1"></i>Criar Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado, iniciando FullCalendar...');
            
            var calendarEl = document.getElementById('calendar');
            
            if (!calendarEl) {
                console.error('Elemento calendar não encontrado!');
                return;
            }
            
            // Limpar conteúdo de loading
            calendarEl.innerHTML = '';
            calendarEl.style.background = 'white';
            calendarEl.style.border = 'none';
            
            // Preparar eventos para o FullCalendar (usando JSON seguro)
            @php
                $eventsForJs = [];
                if ($events && $events->getItems()) {
                    foreach ($events->getItems() as $idx => $event) {
                        $start = $event->getStart();
                        $end = $event->getEnd();
                        $startDateTime = $start->getDateTime() ?? $start->getDate();
                        $endDateTime = $end->getDateTime() ?? $end->getDate();
                        $isAllDay = $start->getDate() !== null;

                        $eventsForJs[] = [
                            'id' => (string) $idx,
                            'title' => $event->getSummary() ?? 'Sem título',
                            'start' => $startDateTime,
                            // FullCalendar aceita null, manter chave para consistência
                            'end' => $endDateTime ?? null,
                            'allDay' => $isAllDay,
                            'description' => $event->getDescription() ?? '',
                            'location' => $event->getLocation() ?? '',
                            'color' => $isAllDay ? '#F7C548' : '#437c90',
                            'textColor' => $isAllDay ? '#255957' : 'white',
                        ];
                    }
                }
            @endphp
            var events = @json($eventsForJs);

            console.log('Eventos carregados:', events);
            
            // Adicionar evento de teste se não houver eventos
            if (!Array.isArray(events) || events.length === 0) {
                events.push({
                    id: 'teste',
                    title: 'Evento de Teste',
                    start: new Date(),
                    allDay: false,
                    description: 'Este é um evento de teste para verificar se o calendário está funcionando.',
                    color: '#437c90',
                    textColor: 'white'
                });
            }

            try {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events,
                eventClick: function(info) {
                    showEventModal(info.event);
                },
                dateClick: function(info) {
                    // Mudar para visualização de semana quando clicar em um dia
                    calendar.changeView('timeGridWeek', info.dateStr);
                },
                select: function(info) {
                    // Abrir modal quando selecionar um período
                    showCreateEventModal(info.start, info.end);
                },
                eventMouseEnter: function(info) {
                    info.el.style.cursor = 'pointer';
                },
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventDisplay: 'block',
                height: 'auto',
                aspectRatio: 1.8,
                nowIndicator: true,
                dayMaxEventRows: 3,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                weekends: true,
                firstDay: 0, // Domingo
                showNonCurrentDates: true,
                fixedWeekCount: false,
                selectable: true,
                selectMirror: true,
                selectOverlap: false,
                unselectAuto: false,
                selectLongPressDelay: 100
            });

                calendar.render();
                console.log('Calendário renderizado com sucesso');
            } catch (error) {
                console.error('Erro ao renderizar calendário:', error);
                calendarEl.innerHTML = '<div class="alert alert-danger">Erro ao carregar o calendário: ' + error.message + '</div>';
            }
        });

        function showEventModal(event) {
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            const title = document.getElementById('eventModalTitle');
            const body = document.getElementById('eventModalBody');
            
            title.textContent = event.title;
            
            let eventDetails = `
                <div class="row">
                    <div class="col-12">
                        <h6><i class="bi bi-calendar3 me-2"></i>Informações do Evento</h6>
                        <hr>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <strong>Início:</strong>
                    </div>
                    <div class="col-8">
                        ${event.start ? event.start.toLocaleString('pt-BR') : 'Não definido'}
                    </div>
                </div>
            `;
            
            if (event.end) {
                eventDetails += `
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Fim:</strong>
                        </div>
                        <div class="col-8">
                            ${event.end.toLocaleString('pt-BR')}
                        </div>
                    </div>
                `;
            }
            
            if (event.extendedProps.description) {
                eventDetails += `
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Descrição:</strong>
                        </div>
                        <div class="col-8">
                            ${event.extendedProps.description}
                        </div>
                    </div>
                `;
            }
            
            if (event.extendedProps.location) {
                eventDetails += `
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Local:</strong>
                        </div>
                        <div class="col-8">
                            <i class="bi bi-geo-alt me-1"></i>${event.extendedProps.location}
                        </div>
                    </div>
                `;
            }
            
            eventDetails += `
                <div class="row">
                    <div class="col-4">
                        <strong>Tipo:</strong>
                    </div>
                    <div class="col-8">
                        ${event.allDay ? '<span class="badge bg-warning text-dark">Dia inteiro</span>' : '<span class="badge bg-primary">Com horário</span>'}
                    </div>
                </div>
            `;
            
            body.innerHTML = eventDetails;
            modal.show();
        }

        function showCreateEventModal(startDate, endDate) {
            const modal = new bootstrap.Modal(document.getElementById('createEventModal'));
            const form = document.getElementById('createEventForm');
            const createBtn = document.getElementById('createEventBtn');
            
            // Limpar formulário
            form.reset();
            createBtn.disabled = true;
            
            // Se não há datas selecionadas, mostrar instruções
            if (!startDate || !endDate) {
                document.getElementById('startDisplay').innerHTML = '<em class="text-muted">Nenhum período selecionado</em>';
                document.getElementById('endDisplay').innerHTML = '<em class="text-muted">Nenhum período selecionado</em>';
                return;
            }
            
            // Formatar datas para exibição
            const startFormatted = formatDateTime(startDate);
            const endFormatted = formatDateTime(endDate);
            
            document.getElementById('startDisplay').innerHTML = startFormatted;
            document.getElementById('endDisplay').innerHTML = endFormatted;
            
            // Definir valores dos campos hidden
            document.getElementById('start').value = startDate.toISOString();
            document.getElementById('end').value = endDate.toISOString();
            
            // Habilitar botão de criar
            createBtn.disabled = false;
            
            modal.show();
        }

        function formatDateTime(date) {
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            };
            
            return date.toLocaleString('pt-BR', options);
        }

        // Habilitar botão quando título for preenchido
        document.getElementById('summary').addEventListener('input', function() {
            const createBtn = document.getElementById('createEventBtn');
            const startValue = document.getElementById('start').value;
            
            if (this.value.trim() && startValue) {
                createBtn.disabled = false;
            } else {
                createBtn.disabled = true;
            }
        });

        function showInstructions() {
            const modal = new bootstrap.Modal(document.getElementById('createEventModal'));
            const form = document.getElementById('createEventForm');
            const createBtn = document.getElementById('createEventBtn');
            
            // Limpar formulário
            form.reset();
            createBtn.disabled = true;
            
            // Mostrar instruções
            document.getElementById('startDisplay').innerHTML = '<em class="text-muted">Arraste e solte no calendário para selecionar</em>';
            document.getElementById('endDisplay').innerHTML = '<em class="text-muted">Arraste e solte no calendário para selecionar</em>';
            
            modal.show();
        }
    </script>
@endsection
