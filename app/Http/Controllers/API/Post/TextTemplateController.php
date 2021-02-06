<?php
namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Post\TextTemplateDestroyRequest;
use App\Http\Requests\Post\TextTemplateListRequest;
use App\Http\Requests\Post\TextTemplateShowRequest;
use App\Http\Requests\Post\TextTemplateStoreRequest;
use App\Http\Requests\Post\TextTemplateUpdateRequest;
use App\Model\TextTemplate;
use Illuminate\Support\Facades\Auth;

class TextTemplateController extends ApiController
{
    public function index(TextTemplateListRequest $request):object
    {
        $data = [
            'notes' => TextTemplate::all('*')
        ];
        return $this->success('',$data);
    }


    public function store(TextTemplateStoreRequest $request)
    {
        $create = TextTemplate::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags
        ]);
        if ($create === null){
            return $this->fail(trans('text_template.create_error'));
        }
        else{
            return $this->success(trans('text_template.create_success'));
        }
    }


    public function show(TextTemplateShowRequest $request,string $uuid):object
    {
        $data = [
            'notes' => TextTemplate::findOrFail($uuid)
        ];
        return $this->success('',$data);
    }


    public function update(TextTemplateUpdateRequest $request):object
    {
        $data = TextTemplate::find($request->id);
        $update = $data->update([
            'id' => $request->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
        ]);
        if ($update === null){
            return $this->fail(trans('text_template.update_error'));
        }
        else{
            return $this->success(trans('text_template.update_success'));
        }
    }


    public function destroy(TextTemplateDestroyRequest $request)
    {
        $data = TextTemplate::where([['id',$request->only('id')],['user_id',Auth::id()]])->first();
        if (!$data){
            return $this->fail(trans('text_template.delete_error'));
        }
        else{
            $data->delete();
            return $this->success(trans('text_template.delete_success'));
        }
    }
}

