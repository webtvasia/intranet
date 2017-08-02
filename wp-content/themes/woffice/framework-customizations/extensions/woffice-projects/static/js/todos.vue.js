/**
 * Task form component used for the Edit / New task
 */

var WofficeTodoForm = {
    props: ['labels', 'todo', 'isNew'],
    methods: {
        isSelected: function (id, assignedMembers) {
            if(typeof assignedMembers === "undefined" ||  assignedMembers === null || assignedMembers.length === 0) {
                return (id === "nope");
            } else {
                var members = [];
                assignedMembers.forEach(function (object) {
                    var currentId = parseInt(object._id);
                    members[currentId] = true;
                });
                return (typeof members[id] === 'boolean');
            }
        },
        assignMember: function (e) {

            var self = this;

            self.todo.assigned = [];

            self.assignedMembers.forEach(function (assigned) {
                if (assigned !== -1) {
                    self.todo.assigned.push(parseInt(assigned));
                }
            });

        }
    },
    data: function () {
        return {
            assignedMembers: []
        }
    },
    mounted: function () {
        var self = this;

        // Datepicker:
        jQuery(this.$el).find('.row .datepicker').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            clearBtn: true,
            orientation: 'bottom'
        }).on('changeDate', function(e) {
            self.todo._formatted_date = e.target.value;
            self.todo.date = e.target.value;
        });

        // We set our current assigned members
        if(typeof self.todo.assigned !== 'undefined' && self.todo.assigned.length !== 0) {
            self.todo.assigned.forEach(function (assigned) {
                self.assignedMembers.push(parseInt(assigned._id));
            });
        }


    },
    template: '\
    <form class="woffice-task-form">\
        <div class="row">\
            <div class="col-md-6">\
                <label for="todo_name">{{ labels.label_name }}</label>\
                <input type="text" name="todo_name" v-model="todo.title" required="required">\
            </div>\
            <div class="col-md-6">\
                <label for="todo_date">{{ labels.label_due_date }}</label>\
                <input type="text" name="todo_date" v-model="todo.date" class="datepicker">\
            </div>\
        </div>\
        <div class="row">\
            <div class="col-md-6 woffice-add-todo-note">\
                <label for="todo_note">{{ labels.label_note }}</label>\
                <textarea rows="2" name="todo_note" v-model="todo.note"></textarea>\
            </div>\
            <div class="col-md-6 woffice-add-todo-assigned">\
                <label for="todo_assigned">{{ labels.label_assign }}</label>\
                <select name="todo_assigned[]" v-model="assignedMembers" @change="assignMember()" class="form-control" multiple>\
                    <option v-for="(member, id) in labels.available_users" :value="parseInt(id)">{{ member }} </option>\
                </select>\
            </div>\
        </div>\
        <div class="clearfix">\
            <div class="pull-left">\
                <label for="todo_urgent">{{ labels.label_urgent }}</label>\
                <input type="checkbox" id="todo_urgent" name="todo_urgent" v-model="todo.urgent" :checked="todo.urgent">\
            </div>\
            <div class="text-right">\
                <button v-if="isNew" href="#" @click.prevent="$root.addTodo(todo)" class="btn btn-default" type="submit"><i class="fa fa-plus-square-o"></i> {{ labels.label_add }}</button>\
                <button v-else href="#" @click.prevent="$root.editTodo(todo)" class="btn btn-default" type="submit"><i class="fa fa-pencil-square-o"></i> {{ labels.label_edit }}</button>\
            </div>\
        </div>\
    </form>'
};

/**
 * Filters list
 */
var filters = {
    all: function (todos) {
        return todos
    },
    urgent: function (todos) {
        return todos.filter(function (todo) {
            return todo.urgent
        })
    },
    done: function (todos) {
        return todos.filter(function (todo) {
            return (todo.done === '1' || todo.done === true);
        })
    }
};

/**
 * Woffice To-do Manager using VUE.JS
 *
 * @since 2.4.0
 */
var wofficeTodo = new Vue({

    // Wrapper
    el: '#project-content-todo',

    // Components
    components: {
        'woffice-task-form': WofficeTodoForm
    },

    // Data handler
    data: {

        exchanger: WOFFICE_TODOS,

        // New to-do
        newTodo: {
            assigned: []
        },

        // Current filter
        currentFilter: 'all',

        // Date filter:
        dueDateFilter: 'no',

        // Todos:
        todos: [],

        // To display the alerts
        isSuccess: false,
        isFailure: false

    },

    computed: {
        filteredTodos: function () {

            var self = this;

            var filteredTodos = filters[self.currentFilter](self.todos);

            if(self.dueDateFilter !== 'no') {
                return (filteredTodos.sort(function (task1, task2) {
                    if(self.dueDateFilter === 'asc') {
                        return (task1._timestamp_date > task2._timestamp_date) ? 1 : -1;
                    }
                    else if(self.dueDateFilter === 'desc') {
                        return (task1._timestamp_date < task2._timestamp_date) ? 1 : -1;
                    }
                    else {
                        return 0;
                    }
                }));
            }

            return filteredTodos;

        }
    },

    mounted: function () {

        this.fetch();

        this.dragAndDrop();

    },

    methods: {

        /**
         * Fetches our todos
         */
        fetch: function () {

            var self = this;

            var loader = new Woffice.loader(jQuery('#project-content-todo'));

            jQuery.ajax({
                type:"POST",
                url: self.exchanger.ajax_url,
                data: {
                    action: 'woffice_todos_fetch',
                    _wpnonce: self.exchanger.nonce,
                    id: self.exchanger.project_id
                },
                success:function(data){
                    data = jQuery.parseJSON(data);
                    if(data.status === 'success') {
                        if (data.todos !== null) {
                            data.todos.forEach(function(todo) {
                                todo._id = self.getId()
                            });
                        }
                        self.todos = data.todos;
                    }
                    loader.remove();
                }
            });

        },

        /**
         * Update our todos
         *
         * @param type {string} - add / delete / check / order / edit
         */
        update: function (type) {

            var self = this;

            var loader = new Woffice.loader(jQuery('#project-content-todo'));

            jQuery.ajax({
                type:"POST",
                url: self.exchanger.ajax_url,
                data: {
                    action: 'woffice_todos_update',
                    _wpnonce: self.exchanger.nonce,
                    id: self.exchanger.project_id,
                    todos: self.todos,
                    type: type
                },
                success:function(data){
                    data = jQuery.parseJSON(data);
                    if (type === "edit" || type === "add") {
                        self.fetch();
                    }
                    loader.remove();
                    self.toggleAlert(data.status);
                }
            });

        },

        /**
         * Add a to-do and update the list
         *
         */
        addTodo: function(to_do) {

            var self = this;

            if (typeof to_do.title === 'undefined')
                return;

            // Default attributes
            to_do._id = self.getId();
            to_do._can_check = true;
            to_do._display_note = false;
            to_do._display_edit = false;
            to_do._is_new = true;
            to_do.email_sent = "not_sent";
            to_do.done = false;

            self.todos.push(to_do);

            // We refresh the assigned field
            self.newTodo = {
                assigned: []
            };

            self.update('add');

        },

        /**
         * Remove a to-do and update the list
         *
         * @param to_do {object}
         */
        removeTodo: function (to_do) {
            this.todos.splice(this.todos.indexOf(to_do), 1);
            this.update('delete');
        },

        /**
         * Order the to-dos and update the list
         */
        orderTodo: function () {
            this.update('order');
        },

        /**
         * Edit a to-do and update the list
         *
         * @param to_do {object}
         */
        editTodo: function (to_do) {
            this.$forceUpdate();
            to_do._is_edited = true;
            this.update('edit');
        },

        /**
         * Toggle the edit form
         *
         * @param to_do {object}
         */
        toggleEit: function (to_do) {
            to_do._display_note = false;
            to_do._display_edit = !to_do._display_edit;
            this.$forceUpdate();
        },

        /**
         * Toggle the note
         *
         * @param to_do {object}
         */
        toggleNote: function (to_do) {
            to_do._display_edit = false;
            to_do._display_note = !to_do._display_note;
            this.$forceUpdate();
        },

        /**
         * Check a to-do
         *
         * @param to_do {object}
         */
        checkTodo: function (to_do) {
            if(to_do.done) {
                to_do.done = false;
            } else {
                to_do.done = true;
            }
            this.$forceUpdate();
            this.update('check');
        },

        /**
         * Set up the drag and drop layout
         */
        dragAndDrop: function () {

            var self = this,
                $wrapper = jQuery(".woffice-tasks-wrapper"),
                from;

            $wrapper.sortable({
                pullPlaceholder: false,
                itemSelector: '.woffice-task',
                placeholder: '<div class="todo-placeholder placeholder"><i class="fa fa-arrow-right"></i></div>',
                handle: ".drag-handle",
                start: function( event, ui ) {
                    jQuery(window).on('resize', function() {
                        if(jQuery(window).width() <= 450) {
                            $wrapper.sortable('disable');
                        } else {
                            $wrapper.sortable('enable');
                        }
                    });
                },
                onDragStart: function ($item, container, _super) {
                    var offset = $item.offset(),
                        pointer = container.rootGroup.pointer;
                    adjustment = {
                        left: pointer.left - offset.left,
                        top: pointer.top - offset.top
                    };
                    _super($item, container);
                },
                onDrag: function ($item, position) {
                    var index = jQuery('.woffice-task').index($item[0]);
                    from = index;
                    $item.css({
                        left: position.left - adjustment.left,
                        top: position.top - adjustment.top
                    });
                },
                onDrop: function ($item, container, _super) {
                    var index = jQuery('.woffice-task').index($item[0]);
                    self.todos.splice(index, 0, self.todos.splice(from, 1)[0]);
                    _super($item, container);
                    self.orderTodo();
                }
            });

        },

        /**
         * Toggles an alert for 5 second
         *
         * @param status {string} - success / fail
         */
        toggleAlert: function (status) {

            var self = this;

            if(status === 'success') {
                self.isSuccess = true;
            } else {
                self.isFailure = true;
            }

            setTimeout(function () {
                self.isSuccess = false;
                self.isFailure = false;
            }, 5000);

        },

        /**
         * Generate an unique ID
         */
        getId : function() {
            return Math.round(Math.random() * 200000);
        }

    }

});