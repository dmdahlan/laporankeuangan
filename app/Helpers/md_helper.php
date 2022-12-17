<?php

use CodeIgniter\I18n\Time;

function rupiah($angka)
{
    return $angka == null ? null : number_format($angka, 0, ',', '.');
}
function inputselect($input)
{
    return $input == null ? null : $input;
}
function inputdate($date)
{
    return $date == null ? null : time::parse($date);
}
function tanggal($date)
{
    return $date == null ? null : date('d-M-Y', strtotime($date));
}
function tglbln($date)
{
    return $date == null ? null : date('d-M', strtotime($date));
}
function tgl($date)
{
    return $date == null ? null : date('d-M-y', strtotime($date));
}
function datefilter($date)
{
    return $date == null ? null : date('Y-m-d', strtotime($date));
}
function dateAll($date)
{
    return $date == null ? null : date('d-M-Y  H:i', strtotime($date));
}
function dateInt($date)
{
    return $date == null ? null : date('d F Y', $date);
}
function bulan($date)
{
    return $date == null ? null : date('F Y', strtotime($date));
}
function monthfilter($date)
{
    return $date == null ? null : date('Y-m', strtotime($date));
}
function dateInv($date)
{
    return $date == null ? null : date('d F Y', strtotime($date));
}
function inputAngka($value)
{
    return $value == null ? null : str_replace('.', '', $value);
}
