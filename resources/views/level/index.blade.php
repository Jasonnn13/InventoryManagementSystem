<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Perizinan Pengguna</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @foreach ([['title' => 'Request', 'users' => $requests, 'empty' => 'Belum ada request.'], ['title' => 'Admin', 'users' => $admins, 'empty' => 'Belum ada admin.'], ['title' => 'Owner', 'users' => $owners, 'empty' => 'Belum ada owner lain.']] as $group)
            <section class="app-panel overflow-hidden">
                <div class="border-b border-[color:var(--app-border)] px-6 py-4">
                    <h3 class="text-lg font-semibold tracking-tight">{{ $group['title'] }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="w-64">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($group['users'] as $user)
                                <tr>
                                    <td class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <div class="flex flex-wrap gap-2">
                                            @if(auth()->user()->isAdmin() && $user->isRequest())
                                                <form action="{{ route('level.approve', $user->id) }}" method="POST">
                                                    @csrf
                                                    <x-primary-button type="submit">Setujui</x-primary-button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->isOwner() && ! $user->isOwner())
                                                <a href="{{ route('level.edit', $user->id) }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Ubah</a>
                                                <form action="{{ route('level.destroy', $user->id) }}" method="POST" onsubmit="return confirmation(event, this)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit" class="delete-button">Hapus</x-danger-button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-neutral-500 dark:text-neutral-400">{{ $group['empty'] }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            function runSwal(options, onConfirm) {
                const invoke = function () {
                    const result = swal(options);
                    if (onConfirm) {
                        result.then(onConfirm);
                    }
                };

                if (typeof swal === 'function') {
                    invoke();
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js';
                script.onload = invoke;
                document.head.appendChild(script);
            }

            function confirmation(event, form) {
                event.preventDefault();
                event.stopPropagation();

                runSwal({
                    title: 'Hapus pengguna ini?',
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }, function (willDelete) {
                    if (willDelete) {
                        form.submit();
                    }
                });

                return false;
            }
        </script>
    @endpush
</x-app-layout>

