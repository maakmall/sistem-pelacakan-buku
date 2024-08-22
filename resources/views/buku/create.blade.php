@extends('layouts.main')

@push('style')
    <!-- Include Choices CSS -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"
    />

    <style>
        .scan-line {
            position: absolute;
            left: 12.5%;
            width: 75%;
            height: 2px; /* Sesuaikan tinggi dengan preferensi */
            background-color: red; /* Ubah warna garis sesuai keinginan */
            animation: scanAnimation 3s linear infinite;
            transform-origin: center;
        }

        @keyframes scanAnimation {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }

        #scanner-container canvas{
            position: absolute;
            left : 0px;
            top: 0px;
        }
    </style>
@endpush

@section('content')
<div class="page-title mb-4">
    <h3>Bibliografi</h3>
</div>
<section class="section">
    <div class="card">
        <form action="/" method="POST" enctype="multipart/form-data" id="biblio">
            <div class="card-header d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mt-2">Tambah Bibliografi</h4>
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="trace"
                    >Lacak Buku</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm mx-3 d-none" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Loading...</span>
                        <span>Simpan</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @csrf
                <div class="row">
                    {{-- Judul --}}
                    <div class="col-md-3">
                        <label for="title">Judul*</label>
                    </div>
                    <div class="col-md-9 form-group">
                        <textarea
                            rows="1"
                            id="title"
                            class="form-control"
                            name="title"
                            placeholder="Judul Buku"
                        ></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Pengarang --}}
                    <div class="col-md-3">
                        <label for="authors">Pengarang</label>
                    </div>
                    <div class="col-md-9 form-group">
                        <select
                            class="choices form-select multiple-remove"
                            id="authors"
                            name="authors[]"
                            multiple
                        >
                            @foreach ($authors as $author)
                                <option value="{{ $author->author_id }}">
                                    {{ $author->author_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block"></div>
                    </div>

                    {{-- ISBN/ISSN --}}
                    <div class="col-md-3">
                        <label for="isbnIssn">ISBN/ISSN</label>
                    </div>
                    <div class="col-md-9 form-group">
                        <input
                            type="text"
                            id="isbnIssn"
                            class="form-control"
                            name="isbn_issn"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Penerbit --}}
                    <div class="col-md-3">
                        <label for="publisherId">Penerbit</label>
                    </div>
                    <div class="col-md-4 form-group">
                        <select
                            id="publisherId"
                            class="form-select choices"
                            name="publisher_id"
                        >
                            <option value="0" disabled selected>-- Pilih --</option>
                            @foreach ($publishers as $publisher)
                                <option value="{{ $publisher->publisher_id }}">
                                    {{ $publisher->publisher_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block"></div>
                    </div>

                    {{-- Tahun Terbit --}}
                    <div class="col-md-2 form-group">
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            name="publish_year"
                            placeholder="Tahun Terbit"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Tempat Terbit --}}
                    <div class="col-md-3 form-group">
                        <select class="form-select choices" name="publish_place_id">
                            <option value="0" selected>-- Pilih --</option>
                            @foreach ($publishPlaces as $publishPlace)
                                <option value="{{ $publishPlace->place_id }}">
                                    {{ $publishPlace->place_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block"></div>
                    </div>

                    {{-- Info Detail Spesifik --}}
                    <div class="col-md-3">
                        <label for="specDetailInfo">
                            Info Detail Spesifik
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <textarea
                            id="specDetailInfo"
                            class="form-control"
                            rows="2"
                            name="spec_detail_info"
                        ></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Abstrak / Catatan --}}
                    <div class="col-md-3">
                        <label for="notes">
                            Abstrak / Catatan
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <textarea
                            id="notes"
                            class="form-control"
                            rows="2"
                            name="notes"
                        ></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Deskripsi Fisik --}}
                    <div class="col-md-3">
                        <label for="collation">
                            Deskripsi Fisik
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <input
                            type="text"
                            id="collation"
                            class="form-control"
                            name="collation"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Gambar Sampul --}}
                    <div class="col-md-3">
                        <label for="image">
                            Gambar Sampul
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <div class="row">
                            <div class="col-2">
                                <img
                                    src="/assets/static/images/image.png"
                                    class="img-fluid img-thumbnail p-0"
                                    id="previewImg"
                                    onerror="this.onerror=null;this.src='/assets/static/images/image.png'"
                                />
                            </div>
                            <div class="col">
                                <input
                                    type="file"
                                    class="form-control"
                                    id="image"
                                    name="image"
                                />
                                <div class="invalid-feedback"></div>
                                <label for="imageUrl" class="my-2">
                                    Atau unduh dari URL:
                                </label>
                                <input
                                    type="text"
                                    id="imageUrl"
                                    class="form-control"
                                    name="image_url"
                                />
                                <div class="invalid-feedback d-block"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Edisi --}}
                    <div class="col-md-3">
                        <label for="edition">
                            Edisi
                        </label>
                    </div>
                    <div class="col-md-4 form-group">
                        <input
                            type="text"
                            id="edition"
                            class="form-control"
                            name="edition"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Bahasa --}}
                    <div class="col-md-2">
                        <label for="languageId">
                            Bahasa
                        </label>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="languageId" class="form-select" name="language_id">
                            @foreach ($languages as $language)
                                <option
                                    value="{{ $language->language_id }}"
                                    @selected($language->language_id == 'id')
                                >
                                    {{ $language->language_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- GMD --}}
                    <div class="col-md-3">
                        <label for="gmdId">
                            GMD
                        </label>
                    </div>
                    <div class="col-md-4 form-group">
                        <select id="gmdId" class="form-select" name="gmd_id">
                            <option value="" selected>-- Pilih --</option>
                            @foreach ($gmds as $gmd)
                                <option value="{{ $gmd->gmd_id }}">
                                    {{ $gmd->gmd_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Tipe Isi --}}
                    <div class="col-md-2">
                        <label for="contentTypeId">
                            Tipe Isi
                        </label>
                    </div>
                    <div class="col-md-3 form-group">
                        <select
                            id="contentTypeId"
                            class="form-select"
                            name="content_type_id"
                        >
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="1">cartographic dataset</option>
                            <option value="2">cartographic image</option>
                            <option value="3">cartographic moving image</option>
                            <option value="4">cartographic tactile image</option>
                            <option value="5">cartographic tactile three-dimensional form</option>
                            <option value="6">cartographic three-dimensional form</option>
                            <option value="7">computer dataset</option>
                            <option value="8">computer program</option>
                            <option value="9">notated movement</option>
                            <option value="10">notated music</option>
                            <option value="11">performed music</option>
                            <option value="12">sounds</option>
                            <option value="13">spoken word</option>
                            <option value="14">still image</option>
                            <option value="15">tactile image</option>
                            <option value="16">tactile notated music</option>
                            <option value="17">tactile notated movement</option>
                            <option value="18">tactile text</option>
                            <option value="19">tactile three-dimensional form</option>
                            <option value="20">text</option>
                            <option value="21">three-dimensional form</option>
                            <option value="22">three-dimensional moving image</option>
                            <option value="23">two-dimensional moving image</option>
                            <option value="24">other</option>
                            <option value="25">unspecified</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Tipe Pembawa --}}
                    <div class="col-md-3">
                        <label for="carrierTypeId">
                            Tipe Pembawa
                        </label>
                    </div>
                    <div class="col-md-4 form-group">
                        <select
                            id="carrierTypeId"
                            class="form-select"
                            name="carrier_type_id"
                        >
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="1">audio cartridge</option>
                            <option value="2">audio cylinder</option>
                            <option value="3">audio disc</option>
                            <option value="4">sound track reel</option>
                            <option value="5">audio roll</option>
                            <option value="6">audiocassette</option>
                            <option value="7">audiotape reel</option>
                            <option value="8">other (audio)</option>
                            <option value="9">computer card</option>
                            <option value="10">computer chip cartridge</option>
                            <option value="11">computer disc</option>
                            <option value="12">computer disc cartridge</option>
                            <option value="13">computer tape cartridge</option>
                            <option value="14">computer tape cassette</option>
                            <option value="15">computer tape reel</option>
                            <option value="16">online resource</option>
                            <option value="17">other (computer)</option>
                            <option value="18">aperture card</option>
                            <option value="19">microfiche</option>
                            <option value="20">microfiche cassette</option>
                            <option value="21">microfilm cartridge</option>
                            <option value="22">microfilm cassette</option>
                            <option value="23">microfilm reel</option>
                            <option value="24">microfilm roll</option>
                            <option value="25">microfilm slip</option>
                            <option value="26">microopaque</option>
                            <option value="27">other (microform)</option>
                            <option value="28">microscope slide</option>
                            <option value="29">other (microscope)</option>
                            <option value="30">film cartridge</option>
                            <option value="31">film cassette</option>
                            <option value="32">film reel</option>
                            <option value="33">film roll</option>
                            <option value="34">filmslip</option>
                            <option value="35">filmstrip</option>
                            <option value="36">filmstrip cartridge</option>
                            <option value="37">overhead transparency</option>
                            <option value="38">slide</option>
                            <option value="39">other (projected image)</option>
                            <option value="40">stereograph card</option>
                            <option value="41">stereograph disc</option>
                            <option value="42">other (stereographic)</option>
                            <option value="43">card</option>
                            <option value="44">flipchart</option>
                            <option value="45">roll</option>
                            <option value="46">sheet</option>
                            <option value="47">volume</option>
                            <option value="48">object</option>
                            <option value="49">other (unmediated)</option>
                            <option value="50">video cartridge</option>
                            <option value="51">videocassette</option>
                            <option value="52">videodisc</option>
                            <option value="53">videotape reel</option>
                            <option value="54">other (video)</option>
                            <option value="55">unspecified</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Tipe Media --}}
                    <div class="col-md-2">
                        <label for="mediaTypeId">
                            Tipe Media
                        </label>
                    </div>
                    <div class="col-md-3 form-group">
                        <select
                            id="mediaTypeId"
                            class="form-select"
                            name="media_type_id"
                        >
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="1">audio</option>
                            <option value="2">computer</option>
                            <option value="3">microform</option>
                            <option value="4">microscopic</option>
                            <option value="5">projected</option>
                            <option value="6">stereographic</option>
                            <option value="7">unmediated</option>
                            <option value="8">video</option>
                            <option value="9">other</option>
                            <option value="10">unspecified</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Judul Seri --}}
                    <div class="col-md-3">
                        <label for="seriesTitle">
                            Judul Seri
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <input
                            type="text"
                            id="seriesTitle"
                            class="form-control"
                            name="series_title"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Kala Terbit --}}
                    <div class="col-md-3">
                        <label for="frequencyId">
                            Kala Terbit
                        </label>
                    </div>
                    <div class="col-md-4 form-group">
                        <select
                            id="frequencyId"
                            class="form-select"
                            name="frequency_id"
                        >
                            <option value="0" selected>-- Tidak digunakan --</option>
                            <option value="1">Weekly</option>
                            <option value="2">Bi-weekly</option>
                            <option value="3">Fourth-Nightly</option>
                            <option value="4">Monthly</option>
                            <option value="5">Bi-Monthly</option>
                            <option value="6">Quarterly</option>
                            <option value="7">3 Times a Year</option>
                            <option value="8">Annualy</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-5">
                        <p class="mt-2">Gunakan untuk koleksi terbitan berseri</p>
                    </div>

                    {{-- Subyek --}}
                    <div class="col-md-3">
                        <label for="subject">Subyek</label>
                    </div>
                    <div class="col-md-9 form-group">
                        <select class="choices form-select multiple-remove" multiple id="subject" name="subjects[]">
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->topic_id }}">
                                    {{ $topic->topic }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block"></div>
                    </div>

                    {{-- Data Biblio Terkait --}}
                    <div class="col-md-3">
                        <label for="biblioRelation">Data Biblio Terkait</label>
                    </div>
                    <div class="col-md-9 form-group">
                        <select class="choices form-select multiple-remove" multiple id="biblioRelation" name="biblio_relation[]">
                            @foreach ($books as $book)
                                <option value="{{ $book->biblio_id }}">
                                    {{ $book->title . ' - ' . $book->edition . ' - ' . $book->publish_year }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block"></div>
                    </div>

                    {{-- Pernyataan Tanggung Jawab --}}
                    <div class="col-md-3">
                        <label for="statementOfResponsibility">
                            Pernyataan Tanggung Jawab
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <input
                            type="text"
                            id="statementOfResponsibility"
                            class="form-control"
                            name="sor"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Sembunyikan di OPAC --}}
                    <div class="col-md-3">
                        <label>
                            Sembunyikan di OPAC
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <div class="form-check">
                            <div class="checkbox">
                                <input
                                    type="radio"
                                    id="show"
                                    class="form-check-input"
                                    name="opac_hide"
                                    value="0"
                                    checked
                                />
                                <label for="show">Tunjukan</label>
                            </div>
                            <div class="checkbox">
                                <input
                                    type="radio"
                                    id="hide"
                                    class="form-check-input"
                                    name="opac_hide"
                                    value="1"
                                />
                                <label for="hide">Sembunyikan</label>
                            </div>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Promosikan di Beranda --}}
                    <div class="col-md-3">
                        <label>
                            Promosikan di Beranda
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <div class="form-check">
                            <div class="checkbox">
                                <input
                                    type="radio"
                                    id="promote"
                                    class="form-check-input"
                                    name="promoted"
                                    value="1"
                                />
                                <label for="promote">Promosikan</label>
                            </div>
                            <div class="checkbox">
                                <input
                                    type="radio"
                                    id="dontPromote"
                                    class="form-check-input"
                                    name="promoted"
                                    value="0"
                                    checked
                                />
                                <label for="dontPromote">Jangan Promosikan</label>
                            </div>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Klasifikasi --}}
                    <div class="col-md-3">
                        <label for="classification">
                            Klasifikasi
                        </label>
                    </div>
                    <div class="col-md-4 form-group">
                        <select
                            id="classification"
                            class="form-select choices"
                            name="classification"
                        >
                            <option value="NONE" selected>-- Pilih --</option>
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->classification }}">
                                    {{ $topic->classification . ' - ' . $topic->topic }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block"></div>
                    </div>

                    {{-- Tipe Koleksi --}}
                    <div class="col-md-2">
                        <label for="collectionType">
                            Tipe Kolesi
                        </label>
                    </div>
                    <div class="col-md-3 form-group">
                        <select
                            id="collectionType"
                            class="form-select choices"
                            name="coll_type_id"
                        >
                            <option value="0" disabled selected>-- Pilih --</option>
                            <option>Textbook</option>
                            <option>Reference</option>
                            <option>Fiksi</option>
                            <option>Bukan Fiksi</option>
                        </select>
                        <div class="invalid-feedback d-block"></div>
                    </div>

                    {{-- Pembuat No. Eksemplar --}}
                    <div class="col-md-3">
                        <label>
                            Pemroses No. Eksemplar
                        </label>
                    </div>
                    <div class="col-md-1">
                        <label for="pattern">
                            Pola
                        </label>
                    </div>
                    <div class="col-md-3 form-group">
                        <input
                            type="text"
                            id="pattern"
                            class="form-control"
                            value="B00000"
                        />
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-1">
                        <label for="from">
                            Dari
                        </label>
                    </div>
                    <div class="col-md-1 form-group">
                        <input
                            type="text"
                            id="from"
                            class="form-control"
                            value="0"
                        />
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-1">
                        <label for="to">
                            Ke
                        </label>
                    </div>
                    <div class="col-md-1 form-group">
                        <input
                            type="text"
                            id="to"
                            class="form-control"
                            value="0"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- No. Panggil --}}
                    <div class="col-md-3">
                        <label for="callNumber">
                            No. Panggil
                        </label>
                    </div>
                    <div class="col-md-9 form-group">
                        <input
                            type="text"
                            id="callNumber"
                            class="form-control"
                            name="call_number"
                        />
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Lampiran Berkas --}}
                    <div class="col-md-3">
                        <label for="attechmentFile">Lampiran Berkas</label>
                    </div>
                    <div class="col-md-9 form-group">
                        <button type="button" class="btn btn-primary btn-sm" id="addAttachment">
                            Tambah Lampiran
                        </button>
                    </div>

                    {{-- Label --}}
                    <div class="col-md-3">
                        <label>Label</label>
                    </div>
                    <div class="col-md-9 ps-4">
                        <div class="row">
                            <div class="form-check col-md-3">
                                <div class="checkbox">
                                    <input type="checkbox" id="newTitle" class="form-check-input label" name="labels[0][]" value="label-new">
                                    <label for="newTitle">New Title</label>
                                </div>
                            </div>
                            <div class="col-md-9 form-group">
                                <input
                                    type="text"
                                    id="new_title"
                                    class="form-control"
                                    name="labels[0][]"
                                    disabled
                                />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-check col-md-3">
                                <div class="checkbox">
                                    <input type="checkbox" id="favoriteTitle" class="form-check-input label" name="labels[1][]" value="label-favorite">
                                    <label for="favoriteTitle">Favorite Title</label>
                                </div>
                            </div>
                            <div class="col-md-9 form-group">
                                <input
                                    type="text"
                                    id="new_title"
                                    class="form-control"
                                    name="labels[1][]"
                                    disabled
                                />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-check col-md-3">
                                <div class="checkbox">
                                    <input type="checkbox" id="multimedia" class="form-check-input label" name="labels[2][]" value="label-multimedia">
                                    <label for="multimedia">Multimedia</label>
                                </div>
                            </div>
                            <div class="col-md-9 form-group">
                                <input
                                    type="text"
                                    id="multimedia"
                                    class="form-control"
                                    name="labels[2][]"
                                    disabled
                                />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // Choice Js Initialitation
    const choicesInstances = {};
    document.querySelectorAll('.choices').forEach((element) => {
        let instance;
        const defaultConfig = {
            classNames: { containerOuter: 'choices mb-0' },
            noResultsText: element.name != 'biblio_relation[]' ? 'Press Enter to add' : 'No results found',
            shouldSort: false,
            duplicateItemsAllowed: false,
            fuseOptions: {
                threshold: 0,
                ignoreLocation: true
            }
        };

        if (element.classList.contains("multiple-remove")) {
            instance = new Choices(element, {
                placeholderValue: element.name != 'biblio_relation[]' ? 'Type to add new options' : null,
                editItems: true,
                removeItemButton: true,
                ...defaultConfig
            });
        } else {
            instance = new Choices(element, defaultConfig);
        }

        if (element.name != 'biblio_relation[]') {
            instance.input.element.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && event.target.value) {
                    const value = event.target.value;

                    // Cek apakah value sudah ada di pilihan
                    if (!element.querySelector(`option[value="${value}"]`)) {
                        // Buat item baru dan tambahkan ke Choice.js
                        instance.setChoices([{
                            value: value,
                            label: value,
                            selected: true
                        }], 'value', 'label', false);

                        // Hapus input value setelah ditambah
                        event.target.value = '';
                        instance.hideDropdown();
                    }
                }
            });
        }

        choicesInstances[element.name] = instance;
    });

    // Preview image
    const image = document.getElementById('image');
    const imageUrl = document.getElementById('imageUrl');
    const previewImg = document.getElementById('previewImg');
    image.addEventListener('change', function () {
        const file = new FileReader();
        file.readAsDataURL(this.files[0]);
        file.onload = (e) => {
            previewImg.src = e.target.result;
        };

        imageUrl.value = '';
    });

    imageUrl.addEventListener('change', function() {
        image.value = '';
        previewImg.src = imageUrl.value;
    });

    // Autofill input
    const fillBook = (book) => {
        const selects = ['authors[]', 'content_type_id', 'media_type_id', 'carrier_type_id', 'subjects[]', 'language_id', 'publish_place_id', 'publisher_id', 'gmd_id', 'biblio_relation[]'];

        for (const [key, value] of Object.entries(book)) {
            console.log(document.querySelector(`[name="${key}"]`), value);

            if (selects.includes(key)) {
                updateComboBox(key, value);
            } else {
                document.querySelector(`[name="${key}"]`).value = value;

                if (key == 'image_url') {
                    imageUrl.dispatchEvent(new Event('change'));
                }
            }
        }
    };

    function updateComboBox(key, valuesToFind) {
        const choicesInstance = choicesInstances[key];

        if (!Array.isArray(valuesToFind)) {
            valuesToFind = [valuesToFind];
        }

        if (choicesInstance) {
            // Clear all existing selections
            choicesInstance.removeActiveItems();

            // Iterate through values to find and select/add options
            valuesToFind.forEach(valueToFind => {
                let found = false;

                choicesInstance.config.choices.forEach(choice => {
                    if (choice.label.toLowerCase().includes(valueToFind?.toLowerCase()) || choice.value.toLowerCase().includes(valueToFind?.toLowerCase())) {
                        choicesInstance.setChoiceByValue(choice.value);

                        found = true;
                    }
                });

                // If the value was not found, add it as a new option
                if (!found && key != 'biblio_relation[]') {
                    choicesInstance.setChoices(
                        [{
                            value: valueToFind,
                            label: valueToFind,
                            selected: true,
                            customProperties: { added: true }
                        }],
                        'value',
                        'label',
                        false
                    );
                }
            });
        } else {
            const comboBox = document.querySelector(`[name="${key}"]`);

            // Clear all existing selections for normal select
            for (let i = 0; i < comboBox.options.length; i++) {
                comboBox.options[i].selected = false;
            }

            // Iterate through values to find and select/add options
            valuesToFind.forEach(valueToFind => {
                let found = false;

                for (let i = 0; i < comboBox.options.length; i++) {
                    if (comboBox.options[i].text.toLowerCase().includes(valueToFind?.toLowerCase()) || comboBox.options[i].value.toLowerCase().includes(valueToFind?.toLowerCase())) {
                        comboBox.options[i].selected = true;
                        found = true;
                    }
                }

                // If the value was not found, add it as a new option
                if (!found && key != 'gmd_id') {
                    if (key == 'language_id' && /^[a-z]{2}(-[A-Z]{2})?$/.test(valueToFind)) {
                        const languageName = new Intl.DisplayNames(['id'], {
                            type: 'language'
                        }).of(valueToFind);

                        const newOption = new Option(languageName, `${valueToFind}:${languageName}`);
                        comboBox.add(newOption);
                        comboBox.value = `${valueToFind}:${languageName}`;

                        return;
                    }

                    const newOption = new Option(valueToFind, valueToFind);
                    comboBox.add(newOption);
                    comboBox.value = valueToFind; // Select the newly added option
                }
            });
        }
    }

    // Automatic call number by classification
    document.getElementById('classification').addEventListener('addItem', function(e) {
        document.getElementById('callNumber').value = e.detail.value != 'NONE'
            ? e.detail.value
            : '';
    });

    // Enable or disable label
    document.querySelectorAll('.label').forEach(function (checkbox) {
        // Menambahkan event listener untuk setiap checkbox
        checkbox.addEventListener('change', function () {
            // Mendapatkan input teks terkait
            const relatedInput = this.closest('.row').querySelector('.form-control');

            if (this.checked) {
                relatedInput.disabled = false;
            } else {
                relatedInput.disabled = true;
                relatedInput.classList.remove('is-invalid');
                relatedInput.value = ''; // Menghapus nilai input ketika dinonaktifkan
            }
        });
    });

    // on click handler lampiran
    let index = 0;
    document.getElementById('addAttachment').addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'mt-3');
        newRow.innerHTML = `
            <div class="col-md-1">
                <label for="attachmentFileTitle${index}">Judul*</label>
            </div>
            <div class="col-md-4 form-group">
                <input type="text" id="attachmentFileTitle${index}" class="form-control" name="attachment_file[${index}][file_title]" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-2">
                <label for="attachmentFileFile${index}">Berkas</label>
            </div>
            <div class="col-md-5 form-group">
                <input type="file" id="attachmentFileFile${index}" class="form-control" name="attachment_file[${index}][file_name]" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-1">
                <label for="attachmentFileUrl${index}">URL</label>
            </div>
            <div class="col-md-4 form-group">
                <input type="text" id="attachmentFileUrl${index}" class="form-control" name="attachment_file[${index}][file_url]" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-2">
                <label for="attachmentFileDesc${index}">Deskripsi</label>
            </div>
            <div class="col-md-5 form-group">
                <textarea id="attachmentFileDesc${index}" class="form-control" name="attachment_file[${index}][file_desc]" rows="1"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-1">
                <label for="attachmentFileAccess${index}">Akses</label>
            </div>
            <div class="col-md-4 form-group">
                <select id="attachmentFileAccess${index}" class="form-select" name="attachment_file[${index}][access_type]">
                    <option value="public">Publik</option>
                    <option value="private">Tertutup</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-3">
                <label>
                    Akses Berdasarkan Tipe
                </label>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <div class="checkbox">
                        <input type="checkbox" id="checkbox1_${index}" class="form-check-input" name="attachment_file[${index}][access_limit][]" value="2">
                        <label for="checkbox1_${index}">Mahasiswa</label>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" id="checkbox2_${index}" class="form-check-input" name="attachment_file[${index}][access_limit][]" value="3">
                        <label for="checkbox2_${index}">Dosen</label>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" id="checkbox3_${index}" class="form-check-input" name="attachment_file[${index}][access_limit][]" value="6">
                        <label for="checkbox3_${index}">Umum</label>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger deleteRow">x</button>
            </div>
        `;

        this.parentElement.appendChild(newRow);

        const deleteButtons = document.querySelectorAll('.deleteRow');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                this.closest('.row').remove();
            });
        });

        index++;
    });

    // on trace handler
    document.getElementById('trace').addEventListener('click', function() {
        Swal.fire({
            title: 'Masukkan nomor ISBN',
            input: 'text',
            inputPlaceholder: '9786230010507',
            showCancelButton: true,
            confirmButtonText: 'Cari',
            cancelButtonText: 'Batal',
            showDenyButton: true,
            denyButtonText: 'Scan Barcode',
            showLoaderOnConfirm: true,
            inputValidator: (isbn) => {
                if (!isbn) {
                    return 'Nomor ISBN belum dimasukkan';
                }

                if (isNaN(isbn)) {
                    return 'Nomor ISBN tidak valid'
                }

                if (isbn.length !== 10 && isbn.length !== 13) {
                    return 'Nomor ISBN harus 10 atau 13 digit'
                }

                if (isbn.length === 10 && (isbn.startsWith('978') || isbn.startsWith('979'))) {
                    return 'Nomor ISBN 13 tidak valid'
                }
            },
            preConfirm: async (isbn) => {
                try {
                    const response = await fetch(`/trace/${isbn}`);

                    if (!response.ok) {
                        if (response.status == 404) {
                            Swal.fire({
                                title: 'Tidak Ditemukan!',
                                text: `Data buku dengan ISBN ${isbn} tidak ditemukan`,
                                icon: 'error'
                            });
                        } else {
                            throw new Error('Pelacakan buku gagal');
                        }
                    } else {
                        return response.json();
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: error.message,
                        icon: 'error'
                    });
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Isi field otomatis
                fillBook(result.value)
            } else if (result.isDenied) {
                // buka swal baru, dengan scanner barcode
                Swal.fire({
                    title: 'Scan Barcode',
                    html: `
                        <div id="scanner-container" class="text-center position-relative">
                            <div class="scan-line"></div>
                        </div>
                    `,
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        // Initialize QuaggaJS
                        Quagga.init({
                            inputStream: {
                                name: "Live",
                                type: "LiveStream",
                                target: document.querySelector('#scanner-container'),
                                constraints: {
                                    width: 320,
                                    height: 140,
                                    facingMode: "environment"
                                }
                            },
                            decoder: {
                                readers: ["ean_reader", "upc_reader"] // Sesuaikan jenis barcode yang didukung
                            },
                            debug: { showCanvas: false }
                        }, function(err) {
                            if (err) {
                                console.log(err);
                                return;
                            }

                            Quagga.start();
                        });

                        Quagga.onDetected(function(result) {
                            const isbn = result.codeResult.code;

                            Swal.fire({
                                title: 'Melacak Buku',
                                text: 'Sedang mencari data buku...',
                                icon: 'info',
                                allowOutsideClick: false,
                                didOpen: async () => {
                                    Swal.showLoading();

                                    try {
                                        const response = await fetch(`/trace/${isbn}`);

                                        if (!response.ok) {
                                            if (response.status == 404) {
                                                Swal.fire({
                                                    title: 'Tidak Ditemukan!',
                                                    text: `Data buku dengan ISBN ${isbn} tidak ditemukan`,
                                                    icon: 'error'
                                                });
                                            } else {
                                                throw new Error('Pelacakan buku gagal');
                                            }
                                        }

                                        const book = await response.json();
                                        Swal.close();
                                        fillBook(book);
                                    } catch (error) {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            text: error.message,
                                            icon: 'error',
                                        });
                                    } finally {
                                        Quagga.stop();
                                    }
                                }
                            });
                        });
                    },
                    willClose: () => {
                        Quagga.stop();
                    }
                });
            }
        });
    });

    // On submit handler
    const biblioForm = document.getElementById('biblio');
    biblioForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const button = biblioForm.querySelector('[type="submit"]');
        button.disabled = true;
        button.children[0].classList.remove('d-none');
        button.children[2].innerHTML = '';

        const data = new FormData(biblioForm);

        if (imageUrl.value) {
            try {
                const proxyUrl = `/proxy-image?url=${encodeURIComponent(imageUrl.value)}`;
                const response = await fetch(proxyUrl);

                if (!response.ok) {
                    throw new Error('Failed to download image');
                }

                const blob = await response.blob();
                data.append('image', blob, 'downloaded_image.jpg');
            } catch (error) {
                imageUrl.nextElementSibling.innerHTML = `Error : ${error.message}`;
            }
        }

        const response = await fetch(biblioForm.action, {
            method: biblioForm.method,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        });

        if (response.ok) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data buku berhasil ditambahkan',
                icon: 'success'
            });

            location.href = '/';
        } else {
            try {
                const { errors } = await response.json();

                biblioForm.querySelectorAll('input[type="text"], input[type="file"], textarea, select').forEach(e => {
                    let name;

                    if (e.name.startsWith('labels')) {
                        name = e.name.replace(/\[(\d+)\]\[\]/, '.$1.1');
                    } else if (e.name.endsWith('[]')) {
                        name = e.name.slice(0, -2);
                    } else {
                        name = e.name.replace(/(\w+)\[(\d+)\]\[(\w+)\]/g, "$1.$2.$3");
                    }

                    if (name in errors) {
                        e.classList.add('is-invalid');
                        if (e.name in choicesInstances) {
                            e.closest('.form-group').children[1].innerHTML = errors[name][0];
                        } else {
                            e.nextElementSibling.innerHTML = errors[name][0];
                        }
                    } else {
                        e.classList.remove('is-invalid');
                        if (e.name in choicesInstances) {
                            e.closest('.form-group').children[1].innerHTML = '';
                        } else {
                            e.nextElementSibling.innerHTML = '';
                        }
                    }
                });
            } catch (error) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Data buku gagal ditambahkan',
                    icon: 'error'
                });
            }
        }

        button.disabled = false;
        button.children[0].classList.add('d-none');
        button.children[2].innerHTML = 'Simpan';
    });
</script>
@endpush
