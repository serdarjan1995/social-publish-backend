<?php


namespace App\Http\Controllers\API\Ticket;

use App\Http\Controllers\API\FileManager\UploadFile;
use App\Http\Requests\Ticket\AvailableTicketAdd;
use App\Http\Requests\Ticket\TicketAddRequest;
use App\Http\Requests\Ticket\TicketGetCategoryListRequest;
use App\Http\Requests\Ticket\TicketGetMessageListRequest;
use App\Model\Ticket\TicketCategories;
use App\Model\Ticket\TicketMessage;
use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use App\Model\Ticket\Ticket;
use Illuminate\Support\Facades\Request;

class TicketController extends ApiController
{

    public function __construct()
    {

    }
    public function getAll()
    {
        $tickets = Ticket::select(
            'id',
            'title',
            'name',
            'services',
            'type',
            'address_id')
            ->where('user_id', Auth::id())
            ->get();
        return $this->success(null,['tickets' => $tickets]);
    }

    public function addTicketMessage($request, $ticket_id) {
        if ($request->hasFile("file")) {
            $uploadFile = new UploadFile();

            $request->resource_type = 2;
            $file = $uploadFile->create($request);
        } else {
            $file = "";
        }
        $new_message = TicketMessage::create([
            "ticket_id" => $ticket_id,
            "send_user_id" => Auth::id(),
            "message" => $request->message,
            "file" => $file
        ]);

        if ($new_message) {
            return true;
        } else {
            return null;
        }
    }

    public function newTicket(TicketAddRequest $request)
    {
        $new_create = Ticket::create([
            "name_surname" => $request->name_surname,
            "category_id" => $request->category_id,
            "created_user" => Auth::id(),
            "email" => $request->email,
        ]);

        if ($new_create) {
            $new_message = $this->addTicketMessage($request, $new_create->id);
            if ($new_message) {
                return $this->success('OK');
            } else {
                return $this->fail('Created failed');
            }
        }
    }

    public function availableTicketAdd(AvailableTicketAdd $request) {
        $new_message = $this->addTicketMessage($request, $request->ticket_id);
        if ($new_message) {
            return $this->success('OK');
        } else {
            return $this->fail('Created failed');
        }
    }

    public function getTicketCategories() {
        return TicketCategories::get();
    }

    public function getTicketMessageList(TicketGetMessageListRequest $request) {
        if (Ticket::where("id", $request->ticket_id)->first()) {
            return Ticket::with("message")->where('id', $request->ticket_id)->first();
        } else {
            return $this->fail('Ticket not found');
        }
    }

    public function getCategoriesTicketList(TicketGetCategoryListRequest $request) {
        $get_list = Ticket::with("message")->where('category_id', $request->category_id)->get();
        if ($get_list) {
            return $get_list;
        } else {
            return $this->fail('Ticket not found');
        }
    }

}
