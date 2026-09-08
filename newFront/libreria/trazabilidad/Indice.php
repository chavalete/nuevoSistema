<?php

class Indice{
        
    private $_indice =Array();
    private $_totalPosiciones;
    
    public function __construct($ninusc = 0 , $mayusc = 1 ) {
        $numerico = "0,1,2,3,4,5,6,7,8,9";
        /*if($ninusc == 1){
            $minuscula = ",a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,r,s,t,u,v,w,x,y,z";
        }*/
        if($mayusc == 1){
            $mayuscula = ",A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
        }
        $this->_indice = explode(',', $numerico . $minuscula . $mayuscula);
        $this->_totalPosiciones = count($this->_indice);
    }
    
    public function getPosicion($posicion){
        return $this->_indice[$posicion];
    }
    
    public function getTotalPosiciones(){
        return $this->_totalPosiciones;
    }
}