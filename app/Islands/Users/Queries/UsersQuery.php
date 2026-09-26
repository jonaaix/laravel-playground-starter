<?php

declare(strict_types=1);

namespace App\Islands\Users\Queries;

use App\Islands\Users\Presenters\UserRowPresenter;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UsersQuery
{
    public const array SORTABLE = ['name', 'email', 'last_login_at', 'created_at'];

    public const array STATUSES = ['active', 'disabled'];

    public const array PER_PAGE_OPTIONS = [30, 50, 100];

    public function __construct(private readonly UserRowPresenter $presenter) {}

    /**
     * @return array{rows: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    public function data(Request $request): array
    {
        $state = self::state($request);

        $query = User::query()
            ->when($state['q'] !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($state): void {
                $query->where('name', 'like', '%'.$state['q'].'%')
                    ->orWhere('email', 'like', '%'.$state['q'].'%');
            }))
            ->when($state['status'] !== '', fn (Builder $query) => $query->where('is_disabled', $state['status'] === 'disabled'))
            ->orderBy($state['sort'], $state['dir'])
            ->orderBy('id');

        $paginator = $query->paginate($state['perPage'], page: $state['page']);

        if ($paginator->isEmpty() && $state['page'] > 1) {
            $paginator = $query->paginate($state['perPage'], page: $paginator->lastPage());
        }

        $currentUser = $request->user();

        return [
            'rows' => $paginator->getCollection()
                ->map(fn (User $user): array => $this->presenter->present($user, $currentUser))
                ->values()
                ->all(),
            'meta' => [
                'paginated' => true,
                'total' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'lastPage' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    /**
     * @return array{q: string, status: string, sort: string, dir: string, page: int, perPage: int}
     */
    public static function state(Request $request): array
    {
        $sort = $request->string('sort')->toString();
        $status = $request->string('status')->toString();
        $perPage = $request->integer('perPage', self::PER_PAGE_OPTIONS[0]);

        return [
            'q' => trim($request->string('q')->toString()),
            'status' => in_array($status, self::STATUSES, true) ? $status : '',
            'sort' => in_array($sort, self::SORTABLE, true) ? $sort : 'created_at',
            'dir' => $request->string('dir')->toString() === 'desc' ? 'desc' : 'asc',
            'page' => max(1, $request->integer('page', 1)),
            'perPage' => in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : self::PER_PAGE_OPTIONS[0],
        ];
    }
}
