
export interface Datasets{
    label:string;
    data:Array<any>;
    fill:boolean;
    hidden:boolean;
}

export interface Counters{
    counter:string;
    datasets:Array<Datasets>;
}

export interface DataInterface{
    cells:Array<string>;
    lacci:any;
    network:any;
    bsc:string;
    site:string;
    vendor:string;
    period:Array<string>;
    counters:Array<Counters>;
}

/*
    normatts:number;
    normseiz:number;
    tch_seizures:number;
    traffic:number;
    t_congestion:number;
    normseiz_nc:number;
    dropped:number;
    dcr:number;
    sd_attempts:number;
    sd_seizures:number;
    sd_blocks:number;
    sd_ppcaid:number;
    intraok:number;
    outok:number;
    obscok:number;
    omscok:number;
    obscfl:number;
    omscfl:number;
    dl_gprs:number;
    ul_gprs:number;
    dl_edge:number;
    ul_edge:number;
    est_dltbf:number;
    fail_dltbf:number;
    dlqual:number;
    dlqual345:number;
    dlqual67:number;
    ulqual:number;
    ulqual345:number;
    ulqual67:number;
*/
