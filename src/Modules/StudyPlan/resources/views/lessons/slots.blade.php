<x-theme::app-layout>
    <div class="container">
        <h1 class="mb-4">Расписание занятий</h1>

        <form method="GET" action="{{ route('lessons.slots') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="group_id" class="form-label">Группа</label>
                <select name="group_id" id="group_id" class="form-select">
                    <option value="">Все группы</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>
                            {{ $group->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="date" class="form-label">Дата</label>
                <input type="date" name="date" id="date" value="{{ $selectedDate }}" class="form-control">
            </div>

            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Фильтровать</button>
            </div>
        </form>

        @forelse ($slots as $date => $daySlots)
            <div class="mb-5">
                <h4 class="text-primary">{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</h4>
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Группа</th>
                        <th>Предмет</th>
                        <th>Преподаватель</th>
                        <th>Время</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($daySlots as $slot)
                        <tr>
                            <td>{{ $slot->slot_order }}</td>
                            <td>{{ $slot->group->title }}</td>
                            <td>{{ $slot->subject->title ?? '—' }}</td>
                            <td>{{ $slot->teacher->name ?? '—' }}</td>
                            <td>{{ $slot->start_time }} – {{ $slot->end_time }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="alert alert-warning">
                Расписание не найдено по выбранным условиям.
            </div>
        @endforelse
    </div>
</x-theme::app-layout>
