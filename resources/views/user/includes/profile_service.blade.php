<tr>
    <td class="align-middle" style="width: 1px;">
        <em class="fab fa-{{ $service }}"></em>
    </td>
    <td class="align-middle" style="width: 1px;">
        <div class="font-weight-bold">{{ ucfirst($service) }}</div>
    </td>
    <td class="align-middle">
        @if($user->getOAuth($service))
            <label class="text-success mb-0">Authorized <em class="fas fa-check-circle"></em></label>
        @else
            <label class="text-secondary mb-0">Unauthorized <em class="fas fa-times-circle"></em></label>
            <a class="btn btn-outline-primary btn-sm float-right" href="{{ url('oauth/' . $service) }}">
                <em class="fas fa-globe"></em> Authorize
            </a>
        @endif
    </td>
</tr>
