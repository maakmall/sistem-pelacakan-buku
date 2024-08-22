@extends('layouts.main')

@section('content')
<div class="page-title mb-4">
    <h3>Bibliografi</h3>
</div>
<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Daftar Bibliografi</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-lg">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>ISBN</th>
                            <th>Penerbit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            {{-- <tr>
                                <td class="text-bold-500">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
                                            <img
                                                src="{{ $book->image }}"
                                                alt="cover"
                                                class="img-fluid img-thumbnail p-0"
                                                onerror="this.onerror=null;this.src='/assets/static/images/image.png'"
                                            />
                                        </div>
                                        <div class="col-md-9 col-lg-10">
                                            <p class="font-bold mb-1">{{ $book->title }}</p>
                                            <span>
                                                {{ $book->pengarang->pluck('author_name')->implode(', ') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $book->isbn_issn }}</td>
                                <td>{{ $book->penerbit?->publisher_name }}</td>
                            </tr> --}}
                            <tr>
                                <td class="text-bold-500">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
                                            <img
                                                src="{{ $book->image }}"
                                                alt="cover"
                                                class="img-fluid img-thumbnail p-0"
                                                onerror="this.onerror=null;this.src='/assets/static/images/image.png'"
                                            />
                                        </div>
                                        <div class="col-md-9 col-lg-10">
                                            <p class="font-bold mb-1">{{ $book->title }}</p>
                                            <span>
                                                {{ $book->authors }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $book->isbn_issn }}</td>
                                <td>{{ $book->publisher_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
