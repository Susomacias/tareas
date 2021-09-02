import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import { Observable } from 'rxjs';
import { Contact} from '../models/contact';
import { global } from './global.service';


@Injectable()
export class ContactService{
    public url: string;
    
 
    constructor(
        private _http: HttpClient
    ){
        this.url = global.url;
    }
   
    

    create(contact):Observable<any>{
        let json = JSON.stringify(contact);
        let params = 'json='+json;
        
        let headers = new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded');
        return this._http.post(this.url+'mail/contact', params, {headers: headers});      
    }  
}
