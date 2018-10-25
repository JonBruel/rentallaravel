@extends('layouts.app')
@section('content')
    <style>
        body {
            padding : 10px ;

        }

        #exTab1 .tab-content {
            color : white;
            background-color: #428bca;
            padding : 5px 15px;
        }


        #exTab1 .nav-pills > li > a {
            border-radius: 4px;
        }

        .mceContentBody {
            background: #AAA;
            filter: none;
            padding-left: 15px;
        }
    </style>
    <h3>{{ __('Edit house') }}</h3>
    <div class="table-responsive">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <table class="table table-striped">
            <tr><td>
            <botton onclick="window.open('/house/browse/{{$id}}','','')" class="btn btn-primary" role="button">{{__('List of files on server')}}</botton>
                    <botton onclick="mcFileManager.open('fileform','url2','','',{rootpath : '{{ $uploaddirdocuments }}', path : '{{ $uploaddirdocuments }}'});" class="btn btn-primary" role="button">{{__('Check and upload documents')}}</botton>
                    <botton onclick="mcImageManager.open('fileform','url1','','',{rootpath : '{{ $uploaddirgraphics }}', path : '{{ $uploaddirgraphics }}'});" class="btn btn-primary" role="button">{{__('Check and upload pictures')}}</botton>
                    <botton onclick="mcImageManager.open('fileform','url3','','',{rootpath : '{{ $uploaddirgallery }}', path : '{{ $uploaddirgallery }}'});" class="btn btn-primary" role="button">{{__('Check and upload gallery')}}</botton>
            </td></tr>
            <tr><td>
                    <div class="form-group row">
                        {!! Form::label('field', __($field), ['class' => 'form-control col-md-9', 'style' => 'margin-left: 5px']) !!}
                        <div class="col-md-2">
                            {{Form::select('field', $fieldselect, $field, ['class' => 'form-control col-md-12', 'style' => 'padding: 1px 0 3px 10px;', 'onchange' => 'window.location.href = "/house/edithousehtml/'.$id.'?field="+this.value;'])}}
                        </div>
                    </div>
            </td></tr>
            {!! Form::open(['url' => 'house/updatehousehtml/'.$id]) !!}
            {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}

            <tr><td>
                    <ul class="nav nav-pills">
                        <?php $first = true; ?>
                        @foreach($cultures as $culture)
                            <?php if ($first) $firstkey = $culture; $first = false; ?>
                            <li class="nav-item">
                                <a class="nav-link {{($culture==$firstkey)?'active':''}}" href="#section{{$field}}{{$culture}}"  role="tab" data-toggle="tab" onclick="reinit('{{$field.'_'.$culture}}')">
                                    {{__($languages[$culture])}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        @foreach($cultures as $culture)
                            <div id="section{{$field}}{{$culture}}" class="tab-pane {{($culture==$firstkey)?'active':''}}">
                                <div class="form-group row">
                                    {!! Form::textarea($field.'['.$culture.']', $models[$culture]->$field, ['id' => $field.'_'.$culture, 'class' => 'col-md-12 col form-control', 'rows' => 50]) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
            </td></tr>
            <tr><td>{!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}</td></tr>
            {!! Form::hidden('field', $field) !!}
            {!! Form::close() !!}

        </table>
    </div>

@include('partials.client_validation')
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('vendor/tiny_mce/tiny_mce.js', config('spp.secure', false))}}"></script>
    <script type="text/javascript" src="{{ asset('vendor/tiny_mce/plugins/filemanager/js/mcfilemanager.js', config('app.secure', false))}}"></script>
    <script  type="text/javascript" src="{{ asset('vendor/tiny_mce/plugins/imagemanager/js/mcimagemanager.js', config('app.secure', false))}}"></script><p class="header" onclick="jQuery('#fileform').toggle()">

    <script type="text/javascript">
        elementnew = "{{$field}}_da_DK";
        elementold = "{{$field}}_da_DK";
        reinit(elementnew);

        function reinit(newelement)
        {
            @if(!in_array($field, $nontinymcefields))
                return;
            @endif

            elementnew = newelement;
            if (elementnew != elementold)
            {
                //tinyMCE.destroy(elementold);
                elementold = elementnew;
                return;
            }

            tinyMCE.init({
                mode:                              "textareas",
                elements:                          newelement,
                content_style:                     "body {padding: 10px}",
                width:                             "800px",
                height:                            "600px",
                resize:                            "both",
                theme_advanced_toolbar_location:   "top",
                theme_advanced_toolbar_align:      "left",
                theme_advanced_statusbar_location: "bottom",
                theme_advanced_resizing:           true,
                plugins: "safari,table,inlinepopups,spellchecker,paste,media,fullscreen,layer,imagemanager,filemanager",
                theme: "advanced",
                language: "{{substr(\App::getLocale(),0,2)}}",
                theme_advanced_buttons1:"bold,italic,strikethrough,underline,forecolor,backcolor,|,bullist,numlist,|,justifyleft,justifyfull,justifycenter,justifyright,outdent,indent,|,link,unlink,wp_more,|,fullscreen,insertimage,insertfile,insertlayer,moveforward,movebackward,absolute,|,media,charmap,|,undo,redo,code",
                theme_advanced_buttons2:"styleselect,formatselect,fontselect,fontsizeselect,|formatselect,|,pastetext,pasteword,removeformat,|,tablecontrols",
                theme_advanced_buttons3:"",
                theme_advanced_buttons4:"",
                content_css : "/css/app.css",
                invalid_elements: "",
                extended_valid_elements : "style,a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],$elements",
                convert_urls : false,
                debug: false,
                imagemanager_rootpath: "/var/www/html/rentallaravel/public/housegraphics/{{$id}}/",
                filemanager_rootpath: "/var/www/html/rentallaravel/public/housedocuments/{{$id}}/"
            });

        }

        contents = [];
        @foreach($cultures as $culture)
            @foreach($fields as $field)
            //contents["{{$culture}}{{$field}}"]="ss";
            @endforeach
        @endforeach
    </script>
@endsection