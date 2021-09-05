import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import { Observable } from 'rxjs';
import { List} from '../models/list';
import { global } from './global.service';
import { ServiceBase } from './servicebase';

@Injectable()
export class ListService extends ServiceBase<List> {
 
    constructor(
        _http: HttpClient
    ) {
        super(_http, 'list');
    }
}