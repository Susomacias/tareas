import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import { Observable } from 'rxjs';
import { Task} from '../models/task';
import { global } from './global.service';
import { ServiceBase } from './servicebase';

@Injectable()
export class TaskService extends ServiceBase<Task> {
 
    constructor(
        _http: HttpClient
    ) {
        super(_http, 'task');
    }
}