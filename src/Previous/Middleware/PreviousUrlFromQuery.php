<?php

namespace Laravelayers\Previous\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravelayers\Previous\PreviousUrl;

class PreviousUrlFromQuery
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($inputName = PreviousUrl::getInputName()) {
            $input = PreviousUrl::getInput();

            $url = PreviousUrl::fullUrl();
            $hash = PreviousUrl::hash($url);

            $previousUrl = PreviousUrl::fullUrl(url()->previous());
            $previousUrlHash = PreviousUrl::hash($previousUrl);

            // If the session contains a request to redirect to the previous URL.
            if ($request->session()->has(PreviousUrl::getRedirectInputName())) {
                $request->session()->forget(PreviousUrl::getRedirectInputName());

                $redirectTo = $input
                    ? $request->session()->get("{$inputName}.hashes.{$input}")
                    : PreviousUrl::getUrl();
            }

            // If the request contains the previous URL.
            if ($input) {
                if (preg_match('/^[a-f0-9]{32}$/', $input)) {
                    $urlHost = explode('.', parse_url($url)['host']);
                    $urlHost = $urlHost[count($urlHost) - 2] . '.' . $urlHost[count($urlHost) - 1];

                    $previous = Arr::except($request->session()->get($inputName, []), ['url', 'hash']);

                    if (Str::endsWith(parse_url($previousUrl)['host'], $urlHost) && $previousUrlHash == $input) {
                        $previous['url'] = $previousUrl;
                        $previous['hash'] = $previousUrlHash;
                    } elseif (!empty($previous['hashes'][$input])) {
                        $previous['url'] = $previous['hashes'][$input];
                        $previous['hash'] = $input;
                    }

                    if (!empty($previous['url'])) {
                        $previous['urls'] = array_merge(
                            $previous['urls'] ?? [],
                            [$hash => $previous['hash']]
                        );

                        $previous['hashes'] = array_merge(
                            $previous['hashes'] ?? [],
                            [$previous['hash'] => $previous['url']]
                        );
                    }
                }

                // If the session contains the previous URL.
            } elseif ($request->session()->has($inputName) && !$request->ajax()) {

                $parsedPreviousUrl = parse_url($previousUrl);

                if (!empty($parsedPreviousUrl['query'])) {
                    parse_str($parsedPreviousUrl['query'], $previousQuery);

                    if (!empty($previousQuery[PreviousUrl::getInputName()])) {
                        $previous = Arr::except($request->session()->get($inputName), ['url', 'hash']);
                    }
                } else {
                    $previous = ['urls' => [], 'hashes' => []];
                }
            }

            if (!empty($previous)) {
                if (!empty($previous['urls'])) {
                    $request->session()->put($inputName, $previous);
                } else {
                    $request->session()->forget($inputName);
                }
            } else {
                $request->session()->forget([$inputName . '.hash', $inputName . '.url']);
            }

            if (!empty($redirectTo)) {
                $request->session()->reflash();

                return redirect($redirectTo);
            }
        }

        return $next($request);
    }
}
