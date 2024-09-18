@extends('layouts.settings')

@section('body')

{!! Form::open(['route' => 'paperforms.templates.store', 'files' => true]) !!}
    <section class="panel panel-settings">
        <header class="panel-heading">
            To use your own template as a paper form, you must upload your file as a PDF document. The first page of your document must leave the first 3 inches (8cm) (including the header) blank because WRMD will fill in that space with the patients intake information. {{ link_to_asset('pdfs/paper-form-template.pdf', 'Download this example file') }} to see what WRMD will add to your template.
        </header>
        <div class="panel-body" style="padding:10px">
            <div class="field-inline">
                {!! Form::label('template', 'PDF Template') !!}
                <div class="data-3-col">
                    <div class="btn btn-file">
                        {!! Form::file('template', ['id' => 'paperforms_template', 'accept' => '.pdf', 'onchange' => 'file_path_display.innerHTML=paperforms_template.files[0].name']) !!}
                        Select Your PDF Template
                    </div>
                    <span id="file_path_display"></span>
                </div>
            </div>
            <div class="field-inline">
                {!! Form::label('name', 'Template Name') !!}
                <div class="data-3-col">
                    {!! Form::text('name', null, ['required']) !!}
                </div>
            </div>
        </div>
    </section>
    {!! Form::submit('Upload PDF Template', ['class' => 'bt']) !!}
{!! Form::close() !!}

@if (count($templates) > 0)
    <hr class="hr">

    <section class="panel panel-settings">
        <header class="panel-heading">Your Available Templates</header>
        <div class="panel-table">
            <table class="table">
                <tr>
                    <th style="width: 40px"></th>
                    <th style="width: 300px">Name</th>
                    <th>Date Added</th>
                </tr>
                @foreach($templates as $template)
                    <tr>
                        <td>
                            <a href="/" onclick="event.preventDefault(); document.getElementById('delete-template-form-{{ $template['slug'] }}').submit();" class="text-danger">
                                @svg('trash')
                            </a>
                            <form id="delete-template-form-{{ $template['slug'] }}" action="{{ route('paperforms.templates.destroy', $template['slug']) }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('delete') }}
                            </form>
                        </td>
                        <td>{{ $template['name'] }}</td>
                        <td>{{ $template['created_at'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </section>
@endif

@stop

