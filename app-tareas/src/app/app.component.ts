import { Component, OnInit, DoCheck } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from './services/user.service';
import { User } from './models/user';
import { global } from './services/global.service';
import { from } from 'rxjs';
import { RouterModule } from '@angular/router';
import { List } from './models/list';
import { ListService } from './services/list.service';
import { PipeService } from './services/pipe.service';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers: [UserService, ListService, PipeService]
})
export class AppComponent implements OnInit, DoCheck {
  public identity;
  public token;
  public url;
  public user: User;
  public avatar: string;
  public origin: string;
  public id;
  public status: string;
  public loading: boolean;
  public userComplete:boolean;
  public list:List;
  public lists;
  pipe:string;

  constructor(
    private _userService: UserService,
    private _listService: ListService,
    private _pipeService: PipeService,
    private _router: Router,
    private _route: ActivatedRoute,
  ) {
    this.url = global.url;
    this.list = new List(null,null,null);
    this.user = new User(1, '', '', '', '', '', '');
    this.origin = this._userService.getOriginLogin();
    this.token      = this._userService.getToken();

    this.identity=localStorage.getItem('identity');
    if(this.identity){
      this.identity = this._userService.getIdentity();

      this.user = new User(
        this.identity.sub,
        this.identity.name,
        this.identity.email, '',
        this.identity.image,
        this.identity.task_list,
        this.identity.newpassword,
      )
    }
  }

  ngOnInit() {
    this.loadUser();

    this._pipeService.sendPipeObservable.subscribe(pipe => {
      this.pipe = pipe;
    })
  }

  ngDoCheck() {
    this.loadUser();
    this.origin = this._userService.getOriginLogin();
    if (this.origin == '{"userlogin"}') { this.origin = 'userlogin' };
  }

  loadUser() {
    this.user = this._userService.getIdentity();
    if (this.user != null) {
      this.avatar = this.user.image;
    }
  }


  coverDisable(){
    localStorage.removeItem('cover');
  }

  pipeChange(pipe: string){
    this._pipeService.sendPipe(pipe)
  }

  createList(form){
    this.loading = true;
    this._listService.create(this.token, this.list).subscribe(
      response => {
        if(response.status ='success'){
          this.status= 'success';
          form.reset();
          this.readerList();
          this.lists=localStorage.getItem('list-list');
          this.pipeChange(this.lists);
          this.loading = false;
        }else{
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    )
  }

  
  readerList() {
    this.loading = true;
    this._listService.getList(this.token, this.user).subscribe(
      response => {
        this.lists = response;
        this.status = "success";
        this.identity = this.user.id
        this.loading = false;
      }, error => {
        console.error(error);
        this.status = "error";
      }
    )
  }

}




