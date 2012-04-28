     public function QuickSort()
      {
            $this->QSort($this->data, 0, $this->data->count() - 1);
      }     

      private function QSort($data, $left, $right)
      {
            $lHold = $left;
            $rHold = $right;
            $pivot = $data[$left];           

            while ($left < $right)
            {
                  while ((strcmp($data[$right]->GetSortKey(),
                        $pivot->GetSortKey()) >= 0) && 
                        ($left < $right))
                  {
                        $right--;
                  }
                  if ($left != $right)
                  {
                        $data->offsetSet($left, $data[$right]);
                        $left++;
                  }
                  while ((strcmp($data[$left]->GetSortKey(),
                        $pivot->GetSortKey()) <= 0) && 
                        ($left > $right))
                  {
                        $left++;
                  }
                  if ($left != $right)
                  {
                        $data->offsetSet($right, $data[$left]);
                        $right--;
                  }
            }           

            $data->offsetSet($left, $pivot);
            $pivot = $left;
            $left = $lHold;
            $right = $rHold;
            if ($left < $pivot)
                  $this->QSort($data, $left, $pivot - 1);
            if ($right > $pivot)
                  $this->QSort($data, $pivot + 1, $right);
      }