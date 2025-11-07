@props(['data', 'columns', 'defaultSort' => null, 'defaultDirection' => 'asc'])

<div x-data="{
    items: {{ json_encode($data) }},
    sortColumn: '{{ $defaultSort }}',
    sortDirection: '{{ $defaultDirection }}',

    sortBy(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }

        this.items = this.items.sort((a, b) => {
            let aVal = this.getNestedValue(a, column);
            let bVal = this.getNestedValue(b, column);

            // Handle null/undefined
            if (aVal === null || aVal === undefined) return 1;
            if (bVal === null || bVal === undefined) return -1;

            // Convert to comparable values
            if (typeof aVal === 'string') aVal = aVal.toLowerCase();
            if (typeof bVal === 'string') bVal = bVal.toLowerCase();

            if (this.sortDirection === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });
    },

    getNestedValue(obj, path) {
        return path.split('.').reduce((current, prop) => current?.[prop], obj);
    }
}">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $key => $column)
                        <th @click="sortBy('{{ $column['field'] }}')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none">
                            <div class="flex items-center space-x-1">
                                <span>{{ $column['label'] }}</span>
                                <span x-show="sortColumn === '{{ $column['field'] }}'">
                                    <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(item, index) in items" :key="index">
                    {{ $slot }}
                </template>
            </tbody>
        </table>
    </div>
</div>
