<section>
    <p class="text-muted mb-4">
        Update your account's profile information and email address.
    </p>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div class="mb-4">
            <label class="form-label fw-bold">Profile Photo</label>
            <div class="d-flex align-items-center gap-3">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo" 
                         class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-grow-1">
                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                           id="profile_photo" name="profile_photo" accept="image/*">
                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block">JPG, PNG, or GIF (Max 2MB)</small>
                    
                    @if($user->profile_photo)
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" 
                                onclick="if(confirm('Remove your profile photo?')) { document.getElementById('removePhotoForm').submit(); }">
                            <i class="bi bi-trash"></i> Remove Photo
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2">
                    <p class="mb-2">Your email address is unverified.</p>
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            Click here to re-send the verification email.
                        </button>
                    </form>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success mt-2 mb-0">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success">
                    <i class="bi bi-check-circle-fill me-1"></i> Saved successfully!
                </span>
            @endif
        </div>
    </form>

    <!-- Separate form for removing photo (outside main form to avoid nesting) -->
    @if($user->profile_photo)
        <form id="removePhotoForm" action="{{ route('profile.photo.delete') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
</section>
