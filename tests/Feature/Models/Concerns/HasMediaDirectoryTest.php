<?php

use App\Models\Concerns\HasMediaDirectory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function mediaUser(): User
{
    $user = User::factory()->create();

    return (new class extends User
    {
        use HasMediaDirectory;

        protected $table = 'users';
    })->newFromBuilder($user->getAttributes());
}

beforeEach(function () {
    config(['filesystems.media_hash_salt' => 'test-salt']);
});

it('places a record under env, table and its salted id hash split 2, 2, 2 and rest', function () {
    $user = mediaUser();
    $hash = md5('test-salt'.$user->getKey());

    expect($user->mediaDirectory())->toBe(implode('/', [
        'testing',
        'users',
        substr($hash, 0, 2),
        substr($hash, 2, 2),
        substr($hash, 4, 2),
        substr($hash, 6),
    ]));
});

it('puts every file of a record into the same directory', function () {
    $user = mediaUser();

    expect($user->mediaPath('avatar.webp'))->toBe($user->mediaDirectory().'/avatar.webp')
        ->and($user->mediaPath('/docs/cv.pdf'))->toBe($user->mediaDirectory().'/docs/cv.pdf');
});

it('refuses to build a path without a salt', function () {
    config(['filesystems.media_hash_salt' => '']);

    mediaUser()->mediaDirectory();
})->throws(LogicException::class, 'MEDIA_HASH_SALT is not set');

it('refuses to build a path for a record that is not stored yet', function () {
    (new class extends User
    {
        use HasMediaDirectory;

        protected $table = 'users';
    })->mediaDirectory();
})->throws(LogicException::class, 'A media directory needs a stored record with a key.');

it('deletes the directory together with the record', function () {
    Storage::fake();
    $user = mediaUser();
    $directory = $user->mediaDirectory();
    Storage::put($user->mediaPath('avatar.webp'), 'image');

    $user->delete();

    expect(Storage::directoryExists($directory))->toBeFalse();
});
