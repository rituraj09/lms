<div>
    <label class="form-label text-muted mb-1" style="font-size:11px; letter-spacing:.4px; text-transform:uppercase;">
        {{ $label }}
    </label>
    @if(!empty($badge) && $badge)
        <div>
            <span class="badge {{ $badgeClass ?? 'bg-secondary' }}">
                {{ $value }}
            </span>
        </div>
    @else
        <p class="mb-0 fw-500" style="font-size:14px;">
            {{ $value }}
        </p>
    @endif
</div>
