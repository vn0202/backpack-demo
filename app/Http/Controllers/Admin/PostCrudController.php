<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Tag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Post;
use Psy\Util\Str;

/**
 * Class PostCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PostCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Post::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/post');
        CRUD::setEntityNameStrings('post', 'posts');

        if (!backpack_auth()->user()->can('create') ){
            CRUD::denyAccess('create');
          }
        if (!backpack_auth()->user()->can('update') ){
               CRUD::denyAccess('update');
          }

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addFilter(
            [
                'type' => 'text',
                'name' => 'title',
                'label' => 'Title'
            ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'title', 'LIKE', "%$value%");

            }
        );
        $this->crud->addFilter([
            'name' => 'category',
            'type' => 'select2',
            'label' => 'Category'
        ], function () {
            return Category::all()->keyBy('id')->pluck('title', 'id')->toArray();;

        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'category_id', $value);
        });
        $this->crud->addFilter([
            'name' => 'tag',
            'type' => 'select2',
            'label' => 'Tags'
        ], function () {
            return Tag::all()->keyBy('id')->pluck('name', 'id')->toArray();;

        }, function ($value) { // if the filter is active
            $this->crud->addClause('whereHas', 'tag', function ($query) use ($value) {
                return $query->where('tag_id', $value);
            });
        });


        $this->crud->addFilter(
            [
                'type' => 'simple',
                'name' => 'active',
                'label' => 'Active'
            ],
            false,
            function ($value) {
                $this->crud->addClause('active');
            }
        );


        $this->crud->addColumn(
            ['name' => 'thumb', // The db column name
                'label' => 'Thumb', // Table column heading
                'type' => 'image',
                'height' => '50px',
                'width' => '50px',
            ]);
        CRUD::column('title');
        CRUD::column('description');
        $this->crud->addColumn(
            ['label' => 'Author', // Table column heading
                'type' => 'select',
                'name' => 'author', // the column that contains the ID of that connected entity;
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\User", // foreign key model
            ]
        );


        $this->crud->addColumn(
            [
                'name' => 'active',
                'label' => 'Active',
                'type' => 'closure',
                'function' => function ($entry) {
                    if ($entry->active == 1) {
                        return "PUBLISH";
                    }
                    return "DRAF";
                }
            ],
        );

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PostRequest::class);
        $this->crud->addField([
            'name' => 'title',
            'label' => "Title",
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Enter your title',
            ], // change the HTML att

        ]);
        //category
        $this->crud->addField(
            [
                'name' => 'category_id', // the db column for the foreign key
                'label' => "Category",
                'type' => 'select2',
                // optional
                'entity' => 'category', // the method that defines the relationship in your Model
                'model' => "App\Models\Category", // foreign key model
                'attribute' => 'title', // foreign key attribute that is shown to user
            ]
        );
        //tag
        $this->crud->addField(
            [
                'type' => "relationship",
                'name' => 'tag', // the method on your model that defines the relationship
                'label' => "Tags",
                'attribute' => "name", // foreign key attribute that is shown to user (identifiable attribute)
                'model' => "App\Models\Tag", // foreign key Eloquent model
                'placeholder' => "Select a tag", // placeholder for the select2 input
                'pivot' => true,

            ]
        );

        $this->crud->addField(
            [//use custom ckeditor with ckeditor 4
                'name' => 'description',
                'label' => 'Description',
                'type' => 'text',
            ],
        );
        $this->crud->addField(
            [   // use custom ckeditor  with ckeditor 4
                'name' => 'content',
                'label' => 'Content',
                'type' => 'customckeditor',
            ],
        );
        $this->crud->addField([
            'label' => "Post Image",
            'name' => "thumb",
            'type' => 'image',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratiB, otherwise set to model accessor function
        ]);

        Post::creating(function ($entry) {
            $entry->author = backpack_auth()->user()->id;
        });


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdatePostRequest::class);
       $this->crud->addField(
          [ 'name'=>'title',
              'label'=>"Title",
              'type'=>'text']

       );
        $this->crud->addField(
            [
                'name' => 'category_id', // the db column for the foreign key
                'label' => "Category",
                'type' => 'select2',
                // optional
                'entity' => 'category', // the method that defines the relationship in your Model
                'model' => "App\Models\Category", // foreign key model
                'attribute' => 'title', // foreign key attribute that is shown to user
            ]
        );
        $this->crud->addField(
            [
                'type' => "relationship",
                'name' => 'tag', // the method on your model that defines the relationship
                'label' => "Tags",
                'attribute' => "name", // foreign key attribute that is shown to user (identifiable attribute)
                'model' => "App\Models\Tag", // foreign key Eloquent model
                'placeholder' => "Select a tag", // placeholder for the select2 input
                'pivot' => true,

            ]
        );
        $this->crud->addField(
            [//use custom ckeditor with ckeditor 4
                'name' => 'description',
                'label' => 'Description',
                'type' => 'text',
            ],
        );
        $this->crud->addField(
            [   // use custom ckeditor  with ckeditor 4
                'name' => 'content',
                'label' => 'Content',
                'type' => 'customckeditor',
            ],
        );

        $this->crud->addField([
            'label' => "Post Image",
            'name' => "base64",
            'type' => 'image',
            'crop' => false, // set to true to allow cropping, false to disable
             'prefix'    => 'uploads/thumbs/' ,// in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;

            'aspect_ratio' => 0, // omit or set to 0 to allow any aspect ratiB, otherwise set to model accessor function
        ]);
        $this->crud->addField(
            [   // radio
                'name'        => 'active', // the name of the db column
                'label'       => 'Status', // the input label
                'type'        => 'radio',
                'options'     => [
                    // the key will be stored in the db, the value will be shown as label;
                    0 => "Draft",
                    1 => "Published"
                ],
                'default'=>$this->crud->getCurrentEntry()->active,
                // optional
                //'inline'      => false, // show the radios all on the same line?
            ],
        );
        // update slug following title
        Post::updating(function ($entry) {
            $entry->slug =  \Illuminate\Support\Str::of($this->crud->getRequest()->title)->slug('-');
        });
    }


    protected function setupShowOperation()
    {
        // by default the Show operation will try to show all columns in the db table,
        // but we can easily take over, and have full control of what columns are shown,
        // by changing this config for the Show operation
        $this->crud->set('show.setFromDb', false);

        // example logic
        $this->crud->addColumn([
            'name' => 'title',
            'label' => "Title",
            'type' => 'text',

        ]);
        $this->crud->addColumn([
            'name' => 'slug',
            'label' => "Slug",
            'type' => 'text',

        ]);
        $this->crud->addColumn([
            'label' => 'Author', // Table column heading
            'type' => 'select',
            'name' => 'author', // the column that contains the ID of that connected entity;
            'entity' => 'user', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\User", // foreign key model


        ]);
        $this->crud->addColumn([
            'name' => 'description',
            'label' => "Description",
            'type' => 'text',

        ]);
        $this->crud->addColumn([
            'label' => 'Parent', // Table column heading
            'type' => 'select',
            'name' => 'category_id', // the column that contains the ID of that connected entity;
            'entity' => 'category', // the method that defines the relationship in your Model
            'attribute' => 'title', // foreign key attribute that is shown to user
            'model' => "App\Models\Category", // foreign key model

        ]);
        $this->crud->addColumn([
            'name' => 'tag', // name of relationship method in the model
            'type' => 'relationship',
            'label' => 'Tags', // Table column heading
        ]);
        $this->crud->addColumn([
            'name' => 'content',
            'label' => "Content",
            'type' => 'textarea',

        ]);
        $this->crud->addColumn([
            'name' => 'thumb',
            'type' => 'image',

        ]);



        // $this->crud->removeColumn('date');
        // $this->crud->removeColumn('extras');

        // Note: if you HAVEN'T set show.setFromDb to false, the removeColumn() calls won't work
        // because setFromDb() is called AFTER setupShowOperation(); we know this is not intuitive at all
        // and we plan to change behaviour in the next version; see this Github issue for more details
        // https://github.com/Laravel-Backpack/CRUD/issues/3108
    }
}
