@extends('layouts.admin')

@section('title', 'Team & Users')
@section('page-title', 'Team & users')

@section('page')
<div class="page-heading-row">
    <div><span class="eyebrow">WHO CAN SIGN IN TO THE WORKROOM</span><h1>Team &amp; users<span class="heading-dot">.</span></h1><p class="page-intro">Add another staff account so more than one person can manage orders.</p></div>
</div>

<div class="dashboard-grid">
    <div class="content-panel orders-panel">
        <div class="panel-topline"><h2>Current users</h2><p>{{ $users->count() }} {{ Str::plural('account', $users->count()) }}</p></div>
        <div class="table-scroll"><table class="data-table"><thead><tr><th>NAME</th><th>EMAIL</th><th>ADDED</th><th></th></tr></thead><tbody>
            @foreach ($users as $user)
                <tr>
                    <td><span class="client-cell"><span class="client-avatar avatar-{{ ($user->id % 5) + 1 }}">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></span></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        @if ($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Remove {{ $user->name }} from the workroom?');">
                                @csrf @method('DELETE')
                                <button class="edit-price-button" type="submit" aria-label="Remove {{ $user->name }}"><x-icon name="trash"/></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody></table></div>
    </div>

    <div class="side-stack">
        <div class="schedule-card">
            <h2>Add a user</h2>
            <form action="{{ route('users.store') }}" method="POST" class="form-stack">
                @csrf
                <label class="field-label">NAME <span class="required-mark">*</span><input class="field-control" name="name" value="{{ old('name') }}" maxlength="120" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">EMAIL <span class="required-mark">*</span><input class="field-control" type="email" name="email" value="{{ old('email') }}" maxlength="160" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">PASSWORD <span class="required-mark">*</span><span class="password-input-wrap"><input class="field-control" type="password" name="password" minlength="10" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span>@error('password')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">CONFIRM PASSWORD <span class="required-mark">*</span><span class="password-input-wrap"><input class="field-control" type="password" name="password_confirmation" minlength="10" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span></label>
                <button class="button button-dark button-wide" type="submit">Add to the workroom <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </div>
</div>
@endsection
