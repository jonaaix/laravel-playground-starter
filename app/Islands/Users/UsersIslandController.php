<?php

declare(strict_types=1);

namespace App\Islands\Users;

use Aaix\LaravelIslandsDatagrid\Concerns\HandlesPreferences;
use Aaix\LaravelIslandsDatagrid\Concerns\HandlesViewProfiles;
use Aaix\LaravelIslandsDatagrid\Preferences\PreferenceStore;
use Aaix\LaravelIslandsDatagrid\ViewProfiles\ViewProfileStore;
use App\Islands\Users\Presenters\UserRowPresenter;
use App\Islands\Users\Queries\UsersQuery;
use App\Islands\Users\State\UsersPreferences;
use App\Islands\Users\State\UsersViewProfiles;
use App\Islands\Users\Writers\UserWriter;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UsersIslandController extends Controller
{
    use HandlesPreferences;
    use HandlesViewProfiles;

    public function __construct(
        private readonly UsersQuery $query,
        private readonly UserWriter $writer,
        private readonly UserRowPresenter $presenter,
    ) {}

    public function data(Request $request): JsonResponse
    {
        $this->authorizeAccess($request);

        return response()->json(['data' => $this->query->data($request)]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAccess($request);

        $data = $request->validate($this->rules(null));

        $user = $this->writer->create($data);

        return response()->json(['data' => $this->presenter->present($user, $request->user())], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorizeAccess($request);

        $data = $request->validate($this->rules($user));

        $user = $this->writer->update($user, $data, $request->user());

        return response()->json(['data' => $this->presenter->present($user, $request->user())]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeAccess($request);

        if (! $this->writer->delete($user, $request->user())) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        return response()->json(['data' => ['id' => $user->id]]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?User $user): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => $user ? ['nullable', 'string', Password::defaults()] : ['required', 'string', Password::defaults()],
            'emailVerified' => ['required', 'boolean'],
            'isDisabled' => ['required', 'boolean'],
        ];
    }

    protected function viewProfiles(): ViewProfileStore
    {
        return UsersViewProfiles::store();
    }

    protected function authorizeViewProfiles(): void
    {
        $this->authorizeAccess(request());
    }

    protected function preferences(): PreferenceStore
    {
        return UsersPreferences::store();
    }

    protected function authorizePreferences(): void
    {
        $this->authorizeAccess(request());
    }

    private function authorizeAccess(Request $request): void
    {
        if (! ($request->user()?->canAccessPanel(Filament::getPanel('admin')) ?? false)) {
            throw new AccessDeniedHttpException;
        }
    }
}
