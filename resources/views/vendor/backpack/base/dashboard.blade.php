@extends(backpack_view('blank'))

@php
    $widgets['before_content'][] = [
        'type'          => 'progress_white',
    'class'         => 'card mb-2',
    'value'         => \App\Models\User::count(),
    'description'   => 'Registered users.',
    'progressClass' => 'progress-bar bg-primary',
    'footer_link'=>route('user.index'),
    'wrapper' => [
    'style' => 'float:left',
]
    ];
     $widgets['before_content'][] = [
       'type'          => 'progress_white',
    'class'         => 'card mb-2',
    'value'         => \App\Models\Post::count(),
    'description'   => 'Total posts.',
    'progressClass' => 'progress-bar bg-primary',
    'footer_link'=>route('post.index'),
     'wrapper' => [
    'style' => 'float:left',
]
    ];
      $widgets['before_content'][] = [
       'type'          => 'progress_white',
    'class'         => 'card mb-2',
    'value'         => \App\Models\Tag::count(),
    'description'   => 'Total tags.',
    'progressClass' => 'progress-bar bg-primary',
   'footer_link'=>route('tag.index'),
 'wrapper' => [
    'style' => 'float:left',
]
    ];

@endphp

@section('content')
@endsection
