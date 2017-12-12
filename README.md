<h2 align="center">Metropol CRB</h2>

<p align="center">
  
<a href="https://travis-ci.org/ngugijames/metropol-crb"><img src="https://travis-ci.org/ngugijames/metropol.svg" alt="Build Status"></a>
  [![Latest Stable Version](https://poser.pugx.org/ngugijames/metropol/v/stable)](https://packagist.org/packages/ngugijames/metropol)
  [![Daily Downloads](https://poser.pugx.org/ngugijames/metropol/d/daily)](https://packagist.org/packages/ngugijames/metropol)
    [![License](https://poser.pugx.org/ngugijames/metropol/license)](https://packagist.org/packages/ngugijames/metropol)
</p>

## About Metropol-CRB
Metropol CRB(Credit Reference Bureau) API wrapper. 
This is a simple PHP client to communicate with the Metropol CRB API.

http://metropol.co.ke/

## Installation

    composer require ngugijames/metropol

## Quick Example
     <?php 
        use Ngugi\Metropol\Metropol;
        
        $metropolPublicKey='dshdhggdid';
        $metropolPrivateKey='UYGSYGA';
        $metropol=new Metropol($metropolPublicKey,$metropolPrivateKey);
    
	    //verify ID number
	    $result=$metropol->identityVerification($id_number); 
    
	    //check deliquency status of an ID number for a loan amount
	    $result=$metropol->deliquencyStatus($id_number, $loan_amount); 
    
	    //check credit Info of an ID number for a loan amount
	    $result=$metropol->creditInfo($id_number, $loan_amount); 
    
	    //check consumer score of ID number
	    $result=$metropol->consumerScore($id_number);
	     
All methods return an array. Check the [docs folder](/Docs) for sample results