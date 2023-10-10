<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertiser;
use App\Models\Offer;
use App\Models\OfferTheme;
use App\Services\WebsocketService;

class OfferController extends Controller
{
    /** Показать форму создания нового оффера */
    public function create()
    {
        return view('pages/add-offer', ['themes' => OfferTheme::all()]);
    }

    /** Сохраните оффер в БД */
    public function store(Request $request)
    {
        $data = $request->all();
        // поиск имени оффера
        if (Offer::where('name', $data['name'])->exists()) {
            return ['result' => 0, 'error' => 'Название оффера уже занято'];
        } else {
            // поиск рекламодателя
            $extendedAdvertisers = Advertiser::join('users', 'users.id','=','advertisers.user_id');
            $advertiserObj = $extendedAdvertisers->where('name', $data['user']);
            if ($advertiserObj->exists()) {
                // добавление оффера
                return ['result' => $this->add($data, $advertiserObj->first()->value('id')), 'offerName' => $data['name']];
            } else {
                return ['result' => 0, 'error' => "Пользователь {$data['user']} не существует"];
            }
        }
    }

    private function add($data, $advertiserId)
    {
        $offer = new Offer();
        $offer->name = $data['name'];
        $url = $data['url'];
        if (mb_stripos($url, '?')) {
            $url = mb_substr($url, 0, mb_stripos($url, '?'));
        }
        $offer->URL =  $url;
        $offer->price = $data['price'];
        $offer->theme_id = OfferTheme::where('name', $data['theme'])->first()->id;
        $offer->advertiser_id = $advertiserId;

        $iAdded = $offer->save();
        if ($iAdded) {
            WebsocketService::send(['type' => 'NEW_OFFER', 'offer' => json_encode($offer)]);
            return 1;
        }  else {
            return 0;
        }
    }
    
    public function destroy($id)
    {
        return ['result' => Offer::find($id)->delete() ? 1 : 0];
    }

    /** установить статус */
    public function status(Request $request)
    {
        $status = $request->all()['status']; 
        $id = $request->all()['id'];
        $offer = Offer::find($id);
        $offer->status = $status === 'true' ? 1 : 0;
        return $offer->save();
    }
}
