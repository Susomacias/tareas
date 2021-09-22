import { Injectable } from "@angular/core";
import { Subject } from 'rxjs';

@Injectable({
    providedIn: 'root'
})

export class PipeService {
    pipe: string;
    private sendPipeSubject = new Subject<string>();
    sendPipeObservable = this.sendPipeSubject.asObservable();

    sendPipe(pipe: string) {
        this.pipe = pipe;
        this.sendPipeSubject.next(pipe);
    }
}