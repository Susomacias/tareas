import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { from } from 'rxjs';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { Task } from '../../models/task';
import { TaskService } from '../../services/task.service';
import { List } from '../../models/list';
import { ListService } from '../../services/list.service';
import { PipeService } from '../../services/pipe.service';
import { global } from '../../services/global.service';
import { ThisReceiver } from '@angular/compiler';
import { MatExpansionModule } from '@angular/material/expansion';
import { browser } from 'protractor';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';
import { features } from 'node:process';
import { CdkDragDrop, moveItemInArray } from '@angular/cdk/drag-drop';
import { Type } from '@angular/core';


@Component({
  selector: 'home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
  providers: [UserService, TaskService, ListService]
})

export class HomeComponent implements OnInit {

  public user: User;
  public identity;
  public token;
  public status: string;
  public id;
  public modal;

  public list: List;
  public lists: Array<List>;
  public _lists;

  public task: Task;
  public tasks: Array<Task>;
  public tasksOfList;

  public loading: boolean;
  public load: boolean;

  panelOpenState = false;
  pipe: string;

  public color: string;

  constructor(
    private _userService: UserService,
    private _listService: ListService,
    private _taskService: TaskService,
    private _pipeService: PipeService,
    private _router: Router,
    private _route: ActivatedRoute,
    private rest: UserService,

  ) {
    this.list = new List(null, null, null);
    this.task = new Task(null, null, null, null, null, 'secondary', null);
    this.user = new User(1, '', '', '', '', '', '');
    this.token = this._userService.getToken();
    this.identity=localStorage.getItem('identity');
    this.load=true;
    
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

    if (!this.user) {
      localStorage.setItem('cover', 'true');
    }
  }

  ngOnInit(): void {
    setTimeout(() => {
      this.load = false;
    }, 5000);

    if (!this.user) {
      localStorage.setItem('cover', 'false');
    }

    this._pipeService.sendPipeObservable.subscribe(
      pipe => { this.pipe = pipe;
      this.lists=JSON.parse(this.pipe);
      this.readerList();
      })

    this.loadUser();
    this.readerList();
    
  }
  

  pipeChange(pipe: string){
    this._pipeService.sendPipe(pipe);
  }

  Modal(modal){
    if (modal == true) {
      this.modal = true
    }
    if (modal == false) {
      this.modal = false
    }
  }

  updateUser(tasks){
    this.user.tasks_list = tasks;
    this._userService.update(this.token, this.user).subscribe(
      response => {
        if (response && response.status) {
          this.status = 'success';

          if (response.change.tasks_list) {
            this.user.tasks_list = response.change.tasks_list;
          }
          this.identity = this.user;
          localStorage.setItem('identity', JSON.stringify(this.identity));
          localStorage.setItem('task-list', JSON.stringify(this.user.tasks_list));
        } else {
          this.status = 'error';
        }
        this.hideBackDrop();
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    );
  }

  addTask(data) {
    this.task = data
    let task_list = localStorage.getItem('task-list');
    if(task_list != 'null'){
      let newtasks = JSON.stringify(this.task);
      task_list = task_list.substring(0, task_list.length - 1);
      task_list = task_list.concat(',' + newtasks + ']');
      task_list = JSON.parse(task_list);
  
      this.updateUser(task_list);
    }
    if (task_list=='null'){
      let newtasks = JSON.stringify(this.task);
      task_list = '[' + newtasks + ']';
      task_list = JSON.parse(task_list);

      this.updateUser(task_list);
    }
   
  }

    delTask(tasks,id){
      tasks=JSON.parse(tasks);
      let index = tasks.map(i=>i.id).indexOf(id);
      tasks.splice(index,1);
      this.updateUser(tasks); 
      this.readerList();
    }

    upTask(tasks,task, id){
      tasks=JSON.parse(tasks);
      let index = tasks.map(i=>i.id).indexOf(id);
      tasks.splice(index,1,task);
      this.updateUser(tasks); 
      this.readerList();
    }

  loadUser() {
    this.user = this._userService.getIdentity();
  }

  drop(event: CdkDragDrop<string[]>) {
    moveItemInArray(this.tasks, event.previousIndex, event.currentIndex);
    let tasks = JSON.stringify(this.tasks);
    this.user.tasks_list = tasks;
    this._userService.update(this.token, this.user).subscribe(
      response => {
        if (response && response.status) {
          this.status = 'success';
          if (response.change.tasks_list) {
            this.user.tasks_list = response.change.tasks_list;
          }
          this.identity = this.user;
          localStorage.setItem('identity', JSON.stringify(this.identity));
          localStorage.setItem('task-list', this.user.tasks_list);
        } else {
          this.status = 'error';
        }
        this.hideBackDrop();
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    );
  }

  //LIST////////////////////////////////////////////

  createList(form) {
    this.loading=true;
    this._listService.create(this.token, this.list).subscribe(
      response => {
        if (response.status = 'success') {
          this.list = response.list;
          this.status = 'success';

        } else {
          this.status = 'error';
        }
        this.hideBackDrop();
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    )
  }

  readerList() {
    this._listService.getList(this.token, this.user).subscribe(
      response => {
        this.lists = response;
        this.status = "success";

        this.identity = this.user.id;
        this._taskService.getList(this.token, this.identity).subscribe(
          response => {
            this.tasks = response;
            this.status = "success";
            if (typeof this.user.tasks_list==='object') {
              this.user.tasks_list= JSON.stringify(this.user.tasks_list); 
            }

            this.tasks = JSON.parse(this.user.tasks_list);
            localStorage.setItem('task-list', this.user.tasks_list);
              this.hideBackDrop();
          }, error => {
            console.error(error);
            this.status = "error";
          }
        )
      }, error => {
        console.error(error);
        this.status = "error";
        this.hideBackDrop();
      }
    )
  }

  updateList(form) {
    this._listService.update(this.token, form);
    this.loading = false;
  }

  deleteList(form) {
    this._listService.delete(this.token, form);
    this.loading = false;
  }

  //TASK////////////////////////////////////////////

  getVal(id) {
    this.id = id;
  }

  createTask(form) {

    this.task.list_id = this.id;
    this._taskService.create(this.token, this.task).subscribe(
      response => {
        if (response.status = 'success') {
          this.task = response.task;
          this.status = 'success';
          this.readerList();
          let task_list = localStorage.getItem('task-list');
            this.addTask(this.task);
            this.loading = false;
        } else {
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    )
  }

  localStorageItem(id: string): string {
    return localStorage.getItem(id);
  }

  updateTask(form) {
 //   this.loading = true;
    this._taskService.update(this.token, form);
    let tasks = this.user.tasks_list;
    let task = form;
    let id = form.id;
    this.upTask(tasks, task, id);
  }

  hideBackDrop() {
 //   this.loading = true;
    let elBackDrop = document.getElementsByClassName('modal-backdrop');
    if(elBackDrop[0]) {
      elBackDrop[0].classList.remove('show');
      elBackDrop[0].classList.remove('modal-backdrop');
    }
  }
  deleteTask(form) {
 //   this.loading = true;
    this._taskService.delete(this.token, form);
    let tasks = this.user.tasks_list;
    let id = form.id;
    this.delTask(tasks, id);
  }

  changeColor(color){
 //   this.loading = true;
    this.task.color=color;
    this.color=color; 
  }
}








