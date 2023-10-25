@extends('layouts.display')
@section('title', trans('app.custom_display'))

<x-displaymonitor displayTitleKey='custom_display' displayMonitor='display3' :setting=$setting/> 