<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Demandes reçues</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">

                <x-flash />

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Date</th>
                                <th class="px-3 py-2 text-left">Employé</th>
                                <th class="px-3 py-2 text-left">Articles</th>
                                <th class="px-3 py-2 text-left">Statut</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($demandes as $d)
                                <tr>
                                    <td class="px-3 py-2">{{ $d->id }}</td>
                                    <td class="px-3 py-2">{{ $d->date }}</td>
                                    <td class="px-3 py-2">{{ $d->employe->name }}</td>
                                    <td class="px-3 py-2">{{ $d->lignes->count() }}</td>
                                    <td class="px-3 py-2"><x-statut-badge :statut="$d->statut" /></td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        <a href="{{ route('demandes.show', $d) }}"
                                           class="px-3 py-1 border border-gray-300 rounded-md text-xs hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Détail
                                        </a>
                                        @if ($d->statut === 'en_attente')
                                            <form method="POST" action="{{ route('demandes.valider', $d) }}" class="inline"
                                                  onsubmit="return confirm('Valider la demande et décrémenter le stock ?')">
                                                @csrf
                                                <button class="px-3 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700">
                                                    Valider
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">Aucune demande.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $demandes->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>