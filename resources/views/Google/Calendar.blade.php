@extends('templateMain')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-calendar3 me-2"></i>Calendário Google
                </h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Atualizar
                    </button>
                    <a href="https://calendar.google.com" target="_blank" class="btn btn-primary">
                        <i class="bi bi-calendar-plus me-1"></i>Abrir Google Calendar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calendário Visual -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3 me-2"></i>{{ now()->format('F Y') }}
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-light" onclick="previousMonth()">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-light" onclick="nextMonth()">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Cabeçalho dos dias da semana -->
                    <div class="calendar-header">
                        <div class="calendar-day-header">Dom</div>
                        <div class="calendar-day-header">Seg</div>
                        <div class="calendar-day-header">Ter</div>
                        <div class="calendar-day-header">Qua</div>
                        <div class="calendar-day-header">Qui</div>
                        <div class="calendar-day-header">Sex</div>
                        <div class="calendar-day-header">Sáb</div>
                    </div>
                    
                    <!-- Grid do calendário -->
                    <div class="calendar-grid">
                        @php
                            $today = now();
                            $firstDayOfMonth = $today->copy()->startOfMonth();
                            $lastDayOfMonth = $today->copy()->endOfMonth();
                            $startDate = $firstDayOfMonth->copy()->startOfWeek();
                            $endDate = $lastDayOfMonth->copy()->endOfWeek();
                            
                            // Organizar eventos por data
                            $eventsByDate = [];
                            if($events->getItems()) {
                                foreach($events->getItems() as $event) {
                                    $start = $event->getStart();
                                    $startDateTime = $start->getDateTime() ?? $start->getDate();
                                    $eventDate = new \DateTime($startDateTime);
                                    $dateKey = $eventDate->format('Y-m-d');
                                    
                                    if(!isset($eventsByDate[$dateKey])) {
                                        $eventsByDate[$dateKey] = [];
                                    }
                                    $eventsByDate[$dateKey][] = $event;
                                }
                            }
                            
                            $currentDate = $startDate->copy();
                        @endphp
                        
                        @while($currentDate->lte($endDate))
                            @php
                                $isCurrentMonth = $currentDate->month == $today->month;
                                $isToday = $currentDate->isSameDay($today);
                                $dateKey = $currentDate->format('Y-m-d');
                                $dayEvents = $eventsByDate[$dateKey] ?? [];
                            @endphp
                            
                            <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                                <div class="calendar-day-number">
                                    {{ $currentDate->day }}
                                </div>
                                
                                @if(count($dayEvents) > 0)
                                    <div class="calendar-events">
                                        @foreach(array_slice($dayEvents, 0, 3) as $event)
                                            @php
                                                $start = $event->getStart();
                                                $isAllDay = $start->getDate() !== null;
                                                $startDateTime = $start->getDateTime() ?? $start->getDate();
                                                $eventDate = new \DateTime($startDateTime);
                                            @endphp
                                            <div class="calendar-event {{ $isAllDay ? 'all-day' : '' }}" 
                                                 title="{{ $event->getSummary() ?? 'Sem título' }}{{ $event->getDescription() ? ' - ' . $event->getDescription() : '' }}">
                                                @if(!$isAllDay)
                                                    <small class="event-time">{{ $eventDate->format('H:i') }}</small>
                                                @endif
                                                <div class="event-title">{{ Str::limit($event->getSummary() ?? 'Sem título', 20) }}</div>
                                            </div>
                                        @endforeach
                                        
                                        @if(count($dayEvents) > 3)
                                            <div class="calendar-event more-events">
                                                +{{ count($dayEvents) - 3 }} mais
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            @php $currentDate->addDay(); @endphp
                        @endwhile
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
                            Clique em "Abrir Google Calendar" para gerenciar seus eventos
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-arrow-right text-primary me-2"></i>
                            Use o botão "Atualizar" para sincronizar os dados
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-arrow-right text-primary me-2"></i>
                            Eventos de dia inteiro aparecem sem horário
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background-color: var(--light-color);
    border-bottom: 2px solid var(--accent-color);
}

.calendar-day-header {
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    color: var(--secondary-color);
    border-right: 1px solid #e0e0e0;
}

.calendar-day-header:last-child {
    border-right: none;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    min-height: 500px;
}

.calendar-day {
    border-right: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
    padding: 8px;
    min-height: 120px;
    position: relative;
    background-color: white;
    transition: background-color 0.2s;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.other-month {
    background-color: #f8f9fa;
    color: #6c757d;
}

.calendar-day.today {
    background-color: #e3f2fd;
    border: 2px solid var(--primary-color);
}

.calendar-day.today .calendar-day-number {
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.calendar-day-number {
    font-weight: 600;
    margin-bottom: 4px;
    color: var(--secondary-color);
}

.calendar-events {
    position: absolute;
    top: 30px;
    left: 4px;
    right: 4px;
    bottom: 4px;
    overflow: hidden;
}

.calendar-event {
    background-color: var(--primary-color);
    color: white;
    padding: 2px 6px;
    margin-bottom: 2px;
    border-radius: 3px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: opacity 0.2s;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.calendar-event:hover {
    opacity: 0.8;
}

.calendar-event.all-day {
    background-color: var(--accent-color);
    color: var(--secondary-color);
}

.calendar-event.more-events {
    background-color: #6c757d;
    text-align: center;
    font-style: italic;
}

.event-time {
    font-size: 0.65rem;
    opacity: 0.9;
    display: block;
}

.event-title {
    font-weight: 500;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    border: none;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.gap-2 {
        width: 100%;
        justify-content: center;
    }
    
    .calendar-day {
        min-height: 80px;
        padding: 4px;
    }
    
    .calendar-event {
        font-size: 0.7rem;
        padding: 1px 4px;
    }
}

@media (max-width: 576px) {
    .calendar-day {
        min-height: 60px;
        padding: 2px;
    }
    
    .calendar-day-header {
        padding: 8px 4px;
        font-size: 0.8rem;
    }
    
    .calendar-event {
        font-size: 0.65rem;
        padding: 1px 3px;
    }
}
</style>

<script>
function previousMonth() {
    // Implementar navegação para mês anterior
    console.log('Mês anterior');
}

function nextMonth() {
    // Implementar navegação para próximo mês
    console.log('Próximo mês');
}
</script>
@endsection
