<?php
function short( $a, $b, $c ){
    // this is a short function and should not trigger anything
}

function warning( ) {
    // this function should trigger a warning


    //5




    //10




    //15




    //20




    //25




    //30
}

function error( ) {
    // this function should trigger a warning


    //5




    //10




    //15




    //20




    //25




    //30




    //35




    //40




    //45




    //50




    //55




    //60
}

class ScopedFunctions
{

    public function short( $a, $b, $c ){
        // this is a short function and should not trigger anything
    }

    public function warning( ) {
        // this function should trigger a warning


        //5




        //10




        //15




        //20




        //25




        //30
    }

    public function error( ) {
        // this function should trigger a warning


        //5




        //10




        //15




        //20




        //25




        //30




        //35




        //40




        //45




        //50




        //55




        //60
    }
}