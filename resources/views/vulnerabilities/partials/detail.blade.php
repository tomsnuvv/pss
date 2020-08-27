@if($detail->type == 'url')
    <a href="{{ $detail->data }}" target="_blank">{{ $detail->data }}</a>
@elseif($detail->type == 'wpvulndb_id')
    WPVulnDB ID: <a href="https://wpvulndb.com/vulnerabilities/{{ $detail->data }}" target="_blank">{{ $detail->data }}</a>
@elseif($detail->type == 'cvss_v3_vector')
    CVSS V3 Vector: {{ $detail->data }}
@elseif($detail->type == 'cvss_v3')
    CVSS V3: {{ $detail->data }}
@elseif($detail->type == 'cvss_v2_vector')
    CVSS V2 Vector: {{ $detail->data }}
@elseif($detail->type == 'cvss_v2')
    CVSS V2: {{ $detail->data }}
@elseif($detail->type == 'cwe')
    CWE: <a href="https://cwe.mitre.org/cgi-bin/jumpmenu.cgi?id={{ $detail->data }}" target="_blank">{{ $detail->data }}</a>
@elseif($detail->type == 'metasploit')
    Metasploit: <a href="https://www.rapid7.com/db/modules/{{ $detail->data }}" target="_blank">{{ $detail->data }}</a>
@elseif($detail->type == 'secunia')
    Secunia ID: <a href="http://secunia.com/advisories/{{ $detail->data }}" target="_blank">{{ $detail->data }}</a>
@elseif($detail->type == 'exploitdb')
    ExploitDB ID: <a href="https://www.exploit-db.com/exploits/{{ $detail->data }}" target="_blank">{{ $detail->data }}</a>
@elseif($detail->type == 'cve')
    CVE: <a href="https://nvd.nist.gov/vuln/detail/{{ $detail->data }}" target="_blank">CVE: {{ $detail->data }}</a>
@else
    {{ $detail->type }}: {{ $detail->data }}
@endif