<div>
    @forelse ($this->activiteiten as $activiteit)
        <div>{{ $activiteit->titel_nl }}</div>
    @empty
    @endforelse
</div>
