@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a data-toggle="modal" data-target="#new" href="" class="btn btn-sm btn-neutral mb-5"><i class="fal fa-plus"></i> {{__('New Language')}}</a>
        <div class="modal fade" id="new" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 h3">{{__('Create Language')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('admin.store.language')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <select class="form-control select" name="language" required>
                                        <option value="af*Afrikaans">Afrikaans</option>
                                        <option value="al*Albanian">Albanian - shqip</option>
                                        <option value="am*Amharic">Amharic - አማርኛ</option>
                                        <option value="ar*Arabic">Arabic - العربية</option>
                                        <option value="an*Aragonese">Aragonese - aragonés</option>
                                        <option value="hy*Armenian">Armenian - հայերեն</option>
                                        <option value="az*Azerbaijani">Azerbaijani - azərbaycan dili</option>
                                        <option value="eu*Basque">Basque - euskara</option>
                                        <option value="be*Belarusian">Belarusian - беларуская</option>
                                        <option value="bn*Bengali">Bengali - বাংলা</option>
                                        <option value="bs*Bosnian">Bosnian - bosanski</option>
                                        <option value="br*Breton">Breton - brezhoneg</option>
                                        <option value="bg*Bulgarian">Bulgarian - български</option>
                                        <option value="ca*Catalan">Catalan - català</option>
                                        <option value="ch*Chinese">Chinese - 中文</option>
                                        <option value="co*Corsican">Corsican</option>
                                        <option value="hr*Croatian">Croatian - hrvatski</option>
                                        <option value="cz*Czech">Czech - čeština</option>
                                        <option value="da*Danish">Danish - dansk</option>
                                        <option value="nl*Dutch">Dutch - Nederlands</option>
                                        <option value="en*English">English</option>
                                        <option value="eo*Esperanto">Esperanto - esperanto</option>
                                        <option value="et*Estonian">Estonian - eesti</option>
                                        <option value="fo*Faroese">Faroese - føroyskt</option>
                                        <option value="fi*Finnish">Finnish - suomi</option>
                                        <option value="fr*French">French - français</option>
                                        <option value="gl*Galician">Galician - galego</option>
                                        <option value="ka*Georgian">Georgian - ქართული</option>
                                        <option value="de*German">German - Deutsch</option>
                                        <option value="gr*Greek">Greek - Ελληνικά</option>
                                        <option value="gn*Guarani">Guarani</option>
                                        <option value="gu*Gujarati">Gujarati - ગુજરાતી</option>
                                        <option value="ha*Hausa">Hausa</option>
                                        <option value="he*Hebrew">Hebrew - עברית</option>
                                        <option value="ie*Hindi">Hindi - हिन्दी</option>
                                        <option value="hu*Hungarian">Hungarian - magyar</option>
                                        <option value="is*Icelandic">Icelandic - íslenska</option>
                                        <option value="id*Indonesian">Indonesian - Indonesia</option>
                                        <option value="ia*Interlingua">Interlingua</option>
                                        <option value="ga*Irish">Irish - Gaeilge</option>
                                        <option value="it*Italian">Italian - italiano</option>
                                        <option value="ja*Japanese">Japanese - 日本語</option>
                                        <option value="kn*Kannada">Kannada - ಕನ್ನಡ</option>
                                        <option value="kk*Kazakh">Kazakh - қазақ тілі</option>
                                        <option value="km*Khmer">Khmer - ខ្មែរ</option>
                                        <option value="ko*Korean">Korean - 한국어</option>
                                        <option value="ku*Kurdish">Kurdish - Kurdî</option>
                                        <option value="ky*Kyrgyz">Kyrgyz - кыргызча</option>
                                        <option value="lo*Lao">Lao - ລາວ</option>
                                        <option value="la*Latin">Latin</option>
                                        <option value="lv*Latvian">Latvian - latviešu</option>
                                        <option value="ln*Lingala">Lingala - lingála</option>
                                        <option value="lt*Lithuanian">Lithuanian - lietuvių</option>
                                        <option value="mk*Macedonian">Macedonian - македонски</option>
                                        <option value="ms*Malay">Malay - Bahasa Melayu</option>
                                        <option value="ml*Malayalam">Malayalam - മലയാളം</option>
                                        <option value="mt*Maltese">Maltese - Malti</option>
                                        <option value="mr*Marathi">Marathi - मराठी</option>
                                        <option value="mn*Mongolian">Mongolian - монгол</option>
                                        <option value="ne*Nepali">Nepali - नेपाली</option>
                                        <option value="no*Norwegian">Norwegian - norsk</option>
                                        <option value="nb*Norwegian Bokmål">Norwegian Bokmål - norsk bokmål</option>
                                        <option value="nn*Norwegian Nynorsk">Norwegian Nynorsk - nynorsk</option>
                                        <option value="oc*Occitan">Occitan</option>
                                        <option value="or*OriyaOriya">OriyaOriya - ଓଡ଼ିଆ</option>
                                        <option value="om*Oromo">Oromo - Oromoo</option>
                                        <option value="ps*Pashto">Pashto - پښتو</option>
                                        <option value="fa*Persian">Persian - فارسی</option>
                                        <option value="pl*Polish">Polish - polski</option>
                                        <option value="pt*Portuguese">Portuguese - português</option>
                                        <option value="pa*Punjabi">Punjabi - ਪੰਜਾਬੀ</option>
                                        <option value="qu*Quechua">Quechua</option>
                                        <option value="ro*">RomanianRomanian - română</option>
                                        <option value="mo*Romanian">Romanian (Moldova) - română (Moldova)</option>
                                        <option value="rm*Romansh">Romansh - rumantsch</option>
                                        <option value="ru*Russian">Russian - русский</option>
                                        <option value="gd*Scottish">Scottish Gaelic</option>
                                        <option value="sr*Serbian">Serbian - српски</option>
                                        <option value="sh*Serbo-Croatian">Serbo-Croatian - Srpskohrvatski</option>
                                        <option value="sn*Shona">Shona - chiShona</option>
                                        <option value="sd*Sindhi">Sindhi</option>
                                        <option value="si*Sinhala">Sinhala - සිංහල</option>
                                        <option value="sk*Slovak">Slovak - slovenčina</option>
                                        <option value="sl*Slovenian">Slovenian - slovenščina</option>
                                        <option value="so*Somali">Somali - Soomaali</option>
                                        <option value="st*Southern Sotho">Southern Sotho</option>
                                        <option value="es*Spanish">Spanish - español</option>
                                        <option value="su*Sundanese">Sundanese</option>
                                        <option value="sw*Swahili">Swahili - Kiswahili</option>
                                        <option value="sv*Swedish">Swedish - svenska</option>
                                        <option value="tg*Tajik">Tajik - тоҷикӣ</option>
                                        <option value="ta*Tamil">Tamil - தமிழ்</option>
                                        <option value="tt*Tatar">Tatar</option>
                                        <option value="te*Telugu">Telugu - తెలుగు</option>
                                        <option value="th*Thai">Thai - ไทย</option>
                                        <option value="ti*Tigrinya">Tigrinya - ትግርኛ</option>
                                        <option value="to*Tongan">Tongan - lea fakatonga</option>
                                        <option value="tr*Turkish">Turkish - Türkçe</option>
                                        <option value="tk*Turkmen">Turkmen</option>
                                        <option value="tw*Twi">Twi</option>
                                        <option value="uk*Ukrainian">Ukrainian - українська</option>
                                        <option value="ur*Urdu">Urdu - اردو</option>
                                        <option value="ug*Uyghur">Uyghur</option>
                                        <option value="uz*Uzbek">Uzbek - o‘zbek</option>
                                        <option value="vi*Vietnamese">Vietnamese - Tiếng Việt</option>
                                        <option value="wa*Walloon">Walloon - wa</option>
                                        <option value="cy*Welsh">Welsh - Cymraeg</option>
                                        <option value="fy*Western Frisian">Western Frisian</option>
                                        <option value="xh*Xhosa">Xhosa</option>
                                        <option value="yi*">YiddishYiddish</option>
                                        <option value="yo*Yoruba">Yoruba - Èdè Yorùbá</option>
                                        <option value="zu*Zulu">Zulu - isiZulu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-block">{{__('Submit')}} </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-buttons">
                            <thead>
                                <tr>
                                    <th>{{__('S/N')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Code')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lang as $k=>$val)
                                <tr>
                                    <td>{{++$k}}.</td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->code}}</td>
                                    <td>
                                        @if($val->status==0)
                                        <span class="badge badge-pill badge-danger">{{__('Disabled')}}</span>
                                        @elseif($val->status==1)
                                        <span class="badge badge-pill badge-info">{{__('Active')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($val->id!=1)
                                        <a href="{{route('admin.edit.language', ['id' => $val->id])}}" class="btn btn-success btn-sm">{{__('Edit Keywords')}}</a>
                                        @if($val->status==0)
                                        <a class='btn btn-danger btn-sm' href="{{route('language.block', ['id' => $val->id])}}">{{__('Block')}}</a>
                                        @else
                                        <a class='btn btn-success btn-sm' href="{{route('language.unblock', ['id' => $val->id])}}">{{__('Unblock')}}</a>
                                        @endif
                                        <a data-toggle="modal" data-target="#delete{{$val->id}}" href="" class="btn btn-danger btn-sm">{{__('Delete')}}</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @foreach($lang as $k=>$val)
                <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                    <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <div class="card bg-white border-0 mb-0">
                                    <div class="card-header">
                                        <h3 class="mb-0">{{__('Are you sure you want to delete this?')}}</h3>
                                    </div>
                                    <div class="card-body px-lg-5 py-lg-5 text-right">
                                        <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                        <a href="{{route('admin.delete.language', ['id' => $val->id])}}" class="btn btn-danger btn-sm">{{__('Proceed')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @stop