@if($set->default_font!="HKGroteskPro")
<style>
    @import url('https://fonts.googleapis.com/css2?family={{$set->default_font}}:wght@300;400;500;700&display=swap');
</style>
@endif
<style>



@font-face{
    font-family:'HKGroteskPro';
    font-weight:400;
    src:url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-Regular.woff2')}}) format("woff2"),
    url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-Regular.woff')}}) format("woff")
  }
  @font-face{
    font-family:'HKGroteskPro';
    font-weight:500;
    src:url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-Medium.woff2')}}) format("woff2"),
    url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-Medium.woff')}}) format("woff")
  }
  @font-face{
    font-family:'HKGroteskPro';
    font-weight:700;
    src:url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-Bold.woff2')}}) format("woff2"),
    url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-Bold.woff')}}) format("woff")
  }  
  @font-face{
    font-family:'HKGroteskPro';
    font-weight:600;
    src:url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-SemiBold.woff2')}}) format("woff2"),
    url({{asset('asset/fonts/HKGroteskPro/HKGroteskPro-SemiBold.woff')}}) format("woff")
  }

body
{
    font-family: "{{$set->default_font}}", sans-serif;
}
pre,code,kbd,samp
{
    font-family: "{{$set->default_font}}", Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
}
.tooltip
{
    font-family: "{{$set->default_font}}", sans-serif;
}
.popover
{
    font-family: "{{$set->default_font}}", sans-serif;
}
.text-monospace
{
    font-family: "{{$set->default_font}}", Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace !important;
}
.btn-group-colors > .btn:before
{
    font-family: "{{$set->default_font}}", sans-serif;
}
.has-danger:after
{
    font-family: '{{$set->default_font}}';
}
.fc-icon
{
    font-family: "{{$set->default_font}}", sans-serif;
}
.ql-container
{
    font-family: "{{$set->default_font}}", sans-serif;
}
</style>