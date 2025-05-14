<x-app-layout>
    <h2 class="text-xl font-semibold mb-4">Review Dry-Run Import</h2>
    <p class="mb-2 text-sm text-gray-600">Preview generated at: {{ $timestamp }}</p>

    @foreach($sheets as $index => $sheet)
        <div class="mb-6">
            <h3 class="font-bold text-lg mb-2">Sheet {{ $index + 1 }}</h3>
            <div class="overflow-x-auto">
                <table class="table-auto border w-full text-sm">
                    @foreach($sheet as $rowIndex => $row)
                        <tr>
                            @foreach($row as $cell)
                                <td class="border px-2 py-1">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endforeach

    <form action="{{ route('audit.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="confirmed_import" value="1">
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Confirm & Import Changes
        </button>
    </form>
</x-app-layout>
