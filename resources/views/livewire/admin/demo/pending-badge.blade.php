<span wire:poll.20s="loadCount">
    @if ($count > 0)
        @if ($mode === 'dot')
            {{-- Red dot indicator (for parent menu) --}}
            <span class="badge-dot ms-md-3" title="{{ $count }} pending request{{ $count > 1 ? 's' : '' }}"></span>
        @else
            {{-- Count badge (for submenu) --}}
            <span class="badge bg-danger rounded-pill ms-md-3">{{ $count > 99 ? '99+' : $count }}</span>
        @endif
    @endif
</span>

@once
    <style>
        .badge-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ef4444;
            margin-left: 6px;
            vertical-align: middle;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, .15);
            animation: pulse-dot 1.6s infinite;
        }

        @keyframes pulse-dot {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, .45);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .menu-link.menu-toggle {
            display: flex;
            align-items: center;
        }

        .menu-link.menu-toggle .badge {
            margin-left: auto;
            margin-right: 8px;
        }
    </style>
@endonce
