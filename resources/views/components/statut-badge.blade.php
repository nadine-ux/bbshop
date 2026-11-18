@props(['statut'])

@php
    $map = [
        'en_attente' => ['En attente',         'badge-warning'],
        'validee'    => ['Préparée / validée', 'badge-primary'],
        'recue'      => ['Reçue',              'badge-success'],
        'refusee'    => ['Refusée',            'badge-danger'],
    ];
    [$label, $classes] = $map[$statut] ?? [$statut, 'badge-secondary'];
@endphp

<span {{ $attributes->merge(['class' => "badge $classes"]) }}>
    {{ $label }}
</span>